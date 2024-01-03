<?php

namespace App\Jobs;

use App\Mail\MailActiveAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $numberCode;
    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($numberCode, $user)
    {
        $this->numberCode = $numberCode;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new MailActiveAccount($this->numberCode, $this->user->name));
    }
}
