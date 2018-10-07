<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\BackupRepository;
use App\Repositories\ConfigurationRepository;

class BackupDatabase extends Command
{
    protected $backup;
    protected $config;

    /**
     *  This command is used to generate backup at certain time.
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BackupRepository $backup, ConfigurationRepository $config)
    {
        $this->backup = $backup;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timezone = $this->config->setDefault();

        if (isTestMode()) {
            $this->error(trans('general.restricted_test_mode_action'));
            return;
        }

        $this->backup->generate();

        $this->info(trans('backup.generated'));
    }
}
