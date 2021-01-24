<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $reportedid;

    public function __construct($reportedid)
    {
        $this->reportedid = $reportedid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendEmail($this->reportedid);
    }

    protected function sendEmail($reportedid)
    {

        // init phpmailer
        $mail = new PHPMailer();
        $mail->isSMTP();

        // debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        // smtp auth
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';
        $email = 'scheduler@tutoringforall.org';
        $clientId = '813639001936-0nefnbeqr0ech4tiusjioh5e77em228m.apps.googleusercontent.com';
        $clientSecret = 'v-w27AgTcIUM7JMNF4Fsz_a1';
        $refreshToken = '1//04x6jOyrrdvgOCgYIARAAGAQSNwF-L9IrmWiyjzjTuSbcPN4bjKf2qd5gl5QfeqWwhFQaJeIiU6h-CZRWAWu_1zPceiW6kFeti1I';

        // coauth2 provider
        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );
        $mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $email,
                ]
            )
        );

        // setup + send email
        $mail->setFrom($email, 'tfa-calendar');
        $mail->isHTML(true);
        $mail->ClearAllRecipients();
        $mail->addAddress(User::find($reportedid)->email, User::find($reportedid)->name);
        $mail->Subject = "You've been reported late/absent";
        $mail->Body = "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> You've been reported late (by more than 10 minutes) or absent to one of your tutoring sessions today. You've received one strike as a result. Three strikes result in a one week probation and lasting record on your account. If you believe this was a mistake, please reply to this email with you full name and email you used for this account, and briefly describe what happened for the session.";
        $mail->AltBody = "If you want to view this message, please use a html supported browser and email client. We apologize for the inconvenience.";
        $mail->send();
    }
}
