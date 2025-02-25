<?php
namespace App\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CustomersRepository;
use App\Services\EmailService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PasswordLessService{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function PasswordLess($email){
      /*   $longPass = uniqid(); */
        $finalPassword = "";
        for($i = 0; $i < 5; $i++){
            $finalPassword = random_int(1, 9) . $finalPassword;
        }

        $mailFunc = new EmailService();
                $subject = "Your One-time Password";
                $to = null;
                $content = '
                    <html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 20px;
                            }
                            .container {
                                max-width: 600px;
                                background: #ffffff;
                                padding: 20px;
                                border-radius: 8px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                text-align: center;
                            }
                            h2 {
                                color: #333;
                            }
                            p {
                                color: #666;
                                font-size: 16px;
                            }
                            .password-key {
                                display: inline-block;
                                background: #007bff;
                                color: #ffffff;
                                padding: 10px 15px;
                                font-size: 18px;
                                font-weight: bold;
                                border-radius: 5px;
                                margin-top: 10px;
                            }
                            .footer {
                                margin-top: 20px;
                                font-size: 12px;
                                color: #999;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <h2>Your Password</h2>
                            <div class="password-key">' . htmlspecialchars($finalPassword) . '</div>
                            <p>If you have any questions, feel free to contact us.</p>
                            <p class="footer">© ' . date("Y") . ' Mini-Axami. All rights reserved.</p>
                        </div>
                    </body>
                    </html>
                ';

                $response = $mailFunc->EmailService($email, $subject, $content, $finalPassword);
                if(isset($response['error'])){
                    return ['error'=>"Email error:" . $response];
                }

                $hashedPassword = password_hash($finalPassword, PASSWORD_BCRYPT);
                $expirationTime = new \DateTime('+1 minutes');

                $this->session->set('realPassword', $hashedPassword);
                $this->session->set('email', $email); /* VRF INTE ID? */

                    return ['success'=>'PasswordService successfully'];
                
               /*  if(!$session->get('realPassword') || !$session->get('email')){
                    return new JsonResponse(['error'=>'Session is broken...']);
                } */
    }
}