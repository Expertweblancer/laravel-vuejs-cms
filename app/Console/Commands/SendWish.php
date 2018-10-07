<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use Illuminate\Console\Command;
use App\Repositories\ConfigurationRepository;

class SendWish extends Command
{
    protected $config;

    /**
     *  This command is used send birthday & anniversary wishesh to users
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-wish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday & Anniversary wishes to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ConfigurationRepository $config)
    {
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
        $this->config->setDefault();

        if (isTestMode()) {
            $this->error(trans('general.restricted_test_mode_action'));
            return;
        }

        $birthdays = \App\Profile::whereRaw('MONTH(date_of_birth) = MONTH(NOW()) AND DAY(date_of_birth) = DAY(NOW())')->get();

        foreach ($birthdays as $birthday) {
            SendMail::dispatch($birthday->User->email, [
                'slug'      => 'birthday-email-user',
                'user'      => $birthday->User,
                'module'    => 'user',
                'module_id' => $birthday->User->id
            ]);
        }

        $anniversaries = \App\Profile::whereRaw('MONTH(date_of_anniversary) = MONTH(NOW()) AND DAY(date_of_anniversary) = DAY(NOW())')->get();

        foreach ($anniversaries as $anniversary) {
            SendMail::dispatch($anniversary->User->email, [
                'slug'      => 'anniversary-email-user',
                'user'      => $anniversary->User,
                'module'    => 'user',
                'module_id' => $anniversary->User->id
            ]);
        }

        $this->info(trans('general.command_completed'));
    }
}
