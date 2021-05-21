<?php

namespace Uasoft\Badaso\Module\Sitemap\Commands;

use Illuminate\Console\Command;

class BadasoSitemapSetup extends Command
{
    private $force;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badaso-sitemap:setup {--force=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->force = $this->option('force');
            if ($this->force == null || $this->force == 'true') {
                $this->force = true;
            } else {
                $this->force = false;
            }

            $this->publishConfig();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function publishConfig()
    {
        $params = [
            '--tag' => 'badaso-sitemap-config',
            '--force' => $this->force,
        ];
        $this->call('vendor:publish', $params);
    }
}
