<?php

namespace SaliBhdr\ValidationRules\Commands;

use Illuminate\Console\Command;
use SaliBhdr\ValidationRules\Contracts\CacheContract;

class RuleClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rule:clear';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'rule:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the rule cache file';

    /**
     * @var CacheContract
     */
    protected $cache;

    /**
     * Create a new route clear command instance.
     *
     * @param CacheContract $cache
     */
    public function __construct(CacheContract $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->cache->flush();

        $this->info('Rule cache cleared successfully.');
    }
}
