<?php

namespace SaliBhdr\ValidationRules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LucidFrame\Console\ConsoleTable;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Methods;
use Symfony\Component\Console\Input\InputOption;

class RuleListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rule:list';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'rule:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List of all cached rules';

    /**
     * The Cache instance
     *
     * @var CacheContract
     */
    protected $cache;

    /**
     * The verb colors for the command.
     *
     * @var array
     */
    protected $verbColors = [
        Methods::ANY     => 'red',
        Methods::GET     => 'blue',
        Methods::HEAD    => '#6C7280',
        Methods::POST    => 'green',
        Methods::PUT     => 'yellow',
        Methods::DELETE  => 'red',
        Methods::CONNECT => '#6C7280',
        Methods::OPTIONS => '#6C7280',
        Methods::PATCH   => 'yellow',
        Methods::PURGE   => '#6C7280',
        Methods::TRACE   => '#6C7280',
    ];

    /**
     * @var Collection
     */
    protected $rules;

    /**
     * Create a new rule command instance.
     *
     * @param CacheContract $cache
     */
    public function __construct(CacheContract $cache)
    {
        parent::__construct();

        $this->cache = $cache;

        $this->rules = collect($this->cache->all());
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!$this->rules->count()) {
            $this->error("Your application doesn't have any cached rule.");

            return;
        }

        if (empty($rules = $this->getRules())) {
            $this->error("Your application doesn't have any rules matching the given criteria.");

            return;
        }

        $this->displayRules($rules);
    }

    /**
     * Compile the rules into a displayable format.
     */
    protected function getRules(): ?array
    {
        $rules = $this->rules->map(function ($rule, $key) {
            return $this->getRuleInformation($key, $rule);
        })->filter()->all();

        if (($sort = $this->option('sort')) !== null) {
            $rules = $this->sortRules($sort, $rules);
        } else {
            $rules = $this->sortRules('key', $rules);
        }

        if ($this->option('reverse')) {
            $rules = array_reverse($rules);
        }

        return $rules;
    }

    /**
     * Get the rule information for a given rule.
     *
     * @param array  $rule
     * @param string $key
     *
     * @return array|null
     */
    protected function getRuleInformation(string $key, array $rule): ?array
    {
        [$type, $key, $method] = explode(':', $key);

        return $this->filterRule([
            'type'   => $type,
            'key'    => $key,
            'method' => $method,
            'rules'  => $rule,
        ]);
    }

    /**
     * Sort the rules by a given element.
     *
     * @param string $sort
     * @param array  $rules
     *
     * @return array|null
     */
    protected function sortRules(string $sort, array $rules): ?array
    {
        return Arr::sort($rules, function ($rule) use ($sort) {
            return $rule[$sort];
        });
    }

    /**
     * Display the rule information on the console.
     *
     * @param array $rules
     */
    protected function displayRules(array $rules): void
    {
        $rules = collect($rules);

        $this->output->writeln(
            $this->option('json') ? $this->asJson($rules) : $this->forCli($rules)
        );
    }

    /**
     * Filter the rule by URI and / or name.
     *
     * @param array $rule
     *
     * @return array|null
     */
    protected function filterRule(array $rule): ?array
    {
        if (
            ($this->option('key') && !Str::contains($rule['key'], $this->option('key'))) ||
            ($this->option('method') && !Str::contains($rule['method'], $this->option('method'))) ||
            ($this->option('type') && !Str::contains($rule['type'], $this->option('type')))
        ) {
            return null;
        }

        if ($prop = $this->option('prop')) {
            $keys = implode('|', array_keys($rule['rules']));

            if (!Str::contains($keys, $prop)) {
                return null;
            }
        }

        return $rule;
    }

    /**
     * Convert the given rules to JSON.
     *
     * @param Collection $rules
     *
     * @return string
     */
    protected function asJson(Collection $rules): string
    {
        return $rules->values()
                     ->toJson();
    }

    /**
     * Convert the given rules to regular CLI output.
     *
     * @param Collection $rules
     *
     * @return string
     */
    protected function forCli(Collection $rules): string
    {
        $content = '';

        foreach ($rules as $rule) {
            $content .= "\ntype: {$this->colorYellow($rule['type'])} | key: {$this->colorPurple($rule['key'])} | method: {$this->colorMethod($rule['method'])}\n";

            $ruleTable = new ConsoleTable();
            $ruleTable->setHeaders(['prop', 'rules']);

            foreach ($rule['rules'] as $prop => $propRule) {
                $ruleTable->addRow([$prop, $this->keyRuleToString($propRule)]);
            }

            $content .= $ruleTable->getTable();
        }

        return $content;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function colorYellow(string $type): string
    {
        return sprintf('<fg=yellow>%s</>', "$type");
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function colorPurple(string $key): string
    {
        return sprintf('<fg=#e80e91>%s</>', "$key");
    }

    /**
     * colors method based on verb colors
     *
     * @param string $method
     *
     * @return string
     */
    protected function colorMethod(string $method): string
    {
        return sprintf('<fg=%s>%s</>', $this->verbColors[$method] ?? 'default', $method);
    }

    /**
     * @param $keyRule
     *
     * @return string
     */
    protected function keyRuleToString($keyRule): string
    {
        $content = '';

        if (is_string($keyRule)) {
            return $keyRule;
        }

        $count = count($keyRule);

        for ($i = 0; $i < $count; $i++) {
            $rule = $keyRule[$i];

            if (is_object($rule)) {
                $rule = $this->objectToString($rule);
            }

            $content .= $rule;

            if ($this->isNotLastItem($i, $count)) {
                $content .= '|';
            }
        }

        return $content;
    }

    /**
     * @param $rule
     *
     * @return string
     */
    protected function objectToString($rule): string
    {
        if (method_exists($rule, '__toString')) {
            $rule = ((string) $rule);
        } else {
            $rule = get_class($rule);
        }

        return $rule;
    }

    /**
     * @param int $i
     * @param int $count
     *
     * @return bool
     */
    public function isNotLastItem(int $i, int $count): bool
    {
        return $i + 1 != $count;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['json', null, InputOption::VALUE_NONE, 'Output the rule list as JSON'],
            ['type', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by type ( class, form, name, uri, custom)'],
            ['method', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by method'],
            ['key', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by key'],
            ['prop', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by given property'],
            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the rules'],
            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (type, key, method) to sort by', 'key'],
        ];
    }
}
