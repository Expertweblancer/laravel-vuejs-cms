<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Repositories\EmailLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\EmailTemplateRepository;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $config;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $config)
    {
        $this->to     = $to;
        $this->config = $config;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailLogRepository $email, EmailTemplateRepository $emailTemplate)
    {
        $template = $emailTemplate->findBySlug($this->config['slug']);

        $user      = isset($this->config['user']) ? $this->config['user'] : null;
        $password  = isset($this->config['password']) ? $this->config['password'] : null;
        $module    = isset($this->config['module']) ? $this->config['module'] : null;
        $module_id = isset($this->config['module_id']) ? $this->config['module_id'] : null;

        if (! $module || ! $module_id) {
            return;
        }

        $mail_data = $emailTemplate->getContent(['template' => $template,'user' => $user, 'password' => $password]);

        $mail['email']   = $this->to;
        $mail['subject'] = $mail_data['subject'];
        $body            = $mail_data['body'];

        \Mail::send('emails.email', compact('body'), function ($message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });

        $email->record([
            'to'        => $mail['email'],
            'subject'   => $mail['subject'],
            'body'      => $body,
            'module'    => $this->config['module'],
            'module_id' => $this->config['module_id']
        ]);
    }
}
