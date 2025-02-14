<?php
namespace App\Services;

/* use Symfony\Component\Mailer\MailerInterface; */
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
class EmailService{

    public function __construct()
    {
  
    }

    public function EmailService($to, $subject, $content, $licenseKey=null){
        $email = (new Email())
            ->from('miniaxami@gmail.com')  // Your sender email
            ->to($to)  // Recipient email
            ->subject($subject)
            ->html($content);

                
                $dsn = 'gmail+smtp://miniaxami@gmail.com:ismyqadzrpyaunfw@smtp.gmail.com:587';
                $transport = Transport::fromDsn($dsn);
            try {
                $mailer = new Mailer($transport);
                $mailer->send($email);
                
                return ["success" => 'Customer registered and email sent'];
            } catch (\Exception $e) {
                return ["error" => "Failed to send email: " . $e->getMessage()];
            }
    }
}