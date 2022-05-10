<?php

namespace SaliBhdr\ValidationRules\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use LucidFrame\Console\ConsoleTable;
use Symfony\Component\Console\Input\InputOption;
use SaliBhdr\ValidationRules\Methods;
use SaliBhdr\ValidationRules\Contracts\CacheContract;

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
     * @param  CacheContract  $cache
     */
    public function __construct(CacheContract $cache)
    {
        parent::__construct();

        $this->cache = $cache;

        $this->rules = collect($this->cache->getAll());
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
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
     *
     * @return array
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
     * @param  array  $rule
     * @param  string  $key
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
     * @param  string  $sort
     * @param  array  $rules
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
     * @param  array  $rules
     *
     * @return void
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
     * @param  array  $rule
     *
     * @return array|null
     */
    protected function filterRule(array $rule): ?array
    {
        if (
            ($this->option('key') && !Str::contains($rule['key'], $this->option('key'))) ||
            ($this->getMethodOption() && !Str::contains($rule['method'], $this->getMethodOption())) ||
            ($this->option('type') && !Str::contains($rule['type'], $this->option('type')))
        ) {
            return null;
        }

        if ($this->option('prop')) {
            $keys = implode('|', array_keys($rule['rules']));

            if (!Str::contains($keys, $this->option('prop'))) {
                return null;
            }
        }

        return $rule;
    }

    /**
     * Convert the given rules to JSON.
     *
     * @param  Collection  $rules
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
     * @param  Collection  $rules
     *
     * @return string
     */
    protected function forCli(Collection $rules): string
    {
        $content = '';

        foreach ($rules as $rule) {
            ['type' => $type, 'key' => $key, 'method' => $method] = $rule;

            $type = sprintf('<fg=yellow>%s</>', "$type");

            $key = sprintf('<fg=#e80e91>%s</>', "$key");

            $method = sprintf('<fg=%s>%s</>', $this->verbColors[$method] ?? 'default', $method);

            $content .= "\ntype: {$type} | key: {$key} | method: {$method}\n";

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
     * @param $keyRule
     *
     * @return string
     */
    protected function keyRuleToString($keyRule): string
    {
        $content = '';

        if (is_array($keyRule)) {
            $count = count($keyRule);

            for ($i = 0; $i < $count; $i++) {
                $rule = $keyRule[$i];

                if (is_object($rule)) {
                    if (method_exists($rule, '__toString')) {
                        $rule = ((string) $rule);
                    } else {
                        $rule = get_class($rule);
                    }
                }

                $content .= $rule;

                if ($i + 1 != $count) {
                    $content .= '|';
                }
            }
        } else {
            $content = $keyRule;
        }

        return $content;
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
            ['type', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by type ( form, name, uri, custom)'],
            ['method', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by method'],
            ['key', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by key'],
            ['prop', null, InputOption::VALUE_OPTIONAL, 'Filter the rules by given property'],
            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the rules'],
            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (type, key, method) to sort by', 'key'],
        ];
    }

    protected function getMethodOption(): ?string
    {
        if ($method = $this->option('method')) {
            return strtoupper($method);
        }

        return null;
    }
}
