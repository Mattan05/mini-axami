<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CustomersRepository;
use App\Services\EmailService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Services\PasswordLessService;
use App\Repository\WorkersRepository;

#[Route('/api')]
final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods:['POST'])]
    public function login(Request $request, CustomersRepository $customersRepository, SessionInterface $session, EntityManagerInterface $entityManager): JsonResponse
    {
        
        $loginData = json_decode($request->getContent(), true);

        if(!isset($loginData['email'])){ 
            return new JsonResponse(['error' => 'Missing required fields']);
        }

        $user = $customersRepository->findOneBy(['customer_email'=>$loginData['email']]);

        if(!$user){
            return new JsonResponse(['error'=>'Customer not found']);
        }

       /*  $longPass = uniqid();
        $finalPassword = "";
        for($i = 0; $i < 8; $i++){
            $finalPassword = $longPass[$i] . $finalPassword;
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

                $response = $mailFunc->EmailService($loginData['email'], $subject, $content, $finalPassword);
                if(isset($response['error'])){
                    return new JsonResponse("Email error:" . $response);
                }
             

                $hashedPassword = password_hash($finalPassword, PASSWORD_BCRYPT);
                $expirationTime = new \DateTime('+1 minutes');

          
                
                $session->set('realPassword', $hashedPassword);
                $session->set('email', $loginData['email']); */

                 /*      $user->setTempPassword($hashedPassword); BEFORE SESSION SET
                $user->setTempPasswordExpiration($expirationTime);

                $entityManager->persist($user);
                $entityManager->flush() */
                $passwordService = new PasswordLessService($session);

                $response = $passwordService->PasswordLess($loginData['email']);
            
                if(isset($response['error'])){
                    return new JsonResponse(['error'=>"Error during passwordless emailing"]);
                }
                

                if(!$session->get('realPassword') || !$session->get('email')){
                    return new JsonResponse(['error'=>'Session is broken...']);
                }

               
                return new JsonResponse(['success'=>'Email with one-time password sent']);
    }

    #[Route('/passwordLess', name: 'password_validation', methods:['POST'])]
    public function PasswordValidation(Request $request, WorkersRepository $workersRepository, CustomersRepository $customerRepository, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        /* ATT GÖRA: I data skicka med ett fält för om det är customer eller worker och därefter använd antingen customerrespos eller workerrepos 
        Då behöver jag också påverka så att fetchen blir rätt beroende på worker eller customer. krver lite tid får göra senare.
        Kan också bara göra en service för passwordvalidation eller funktion som jag lägger in i PasswordlessService.php!!!!! 
        Då kan jag bara skicka med flagga för antingen worker eller customer. det lär fungera sen får ajg lösa frontend senare!
        dvs ta denna koden här och lägg in i en service
        */

        if(!isset($data['password'], $data['account_type'])){ 
            return new JsonResponse(['error' => 'Missing required fields']);
        }

        $hashedPassword = "";
        $account_email = '';
        $hashedPassword = $session->get("realPassword");
        $account_email = $session->get('email');
        /* FÅR BARA DETTA I SESSIONS IBLAND */

       /*  return new JsonResponse(['error'=>'customerEmail: ' . $customerEmail . " " . 'hashedPassword: ' . $hashedPassword]); */

        if(!password_verify($data['password'], $hashedPassword)){
            return new JsonResponse(['error'=>'Password is incorrect']);
        }
        if($data['account_type'] === 'worker') {
            $worker = $workersRepository->findOneBy(['worker_email'=>$account_email]);

            if(!isset($worker)){
                return new JsonResponse(['error'=>'Worker could not be found..']);
            }
            $session->invalidate();
            $session->set( 'worker_id', $worker->getId());
            $session->set("user_id", $worker->getId());/* DETTA SKA SEN OCKSÅ VARA WORKERS...? */
            $session->set("role", $worker->getRoles());
            $session->set("name", $worker->getName());
            return new JsonResponse(["success"=>['user_id'=>$worker->getId(), 'role'=>$worker->getRoles(), 'name'=>$worker->getName()] ]);
        }else{
            $customer = $customerRepository->findOneBy(['customer_email'=>$account_email]);
  
            if(!isset($customer)){
                return new JsonResponse(['error'=>'Customer could not be found..']);
            }
            $session->invalidate();
            $session->set('customer_id', $customer->getId());
            $session->set("user_id", $customer->getId());
            $session->set("role", $customer->getRoles());
            $session->set("name", $customer->getName());
            return new JsonResponse(["success"=>['user_id'=>$customer->getId(), 'role'=>$customer->getRoles(), 'name'=>$customer->getName()] ]);
        }

        

        /* ha en timer som efter ett visst tag behöver man skicka om. 30 sekunder */
    }

    #[Route('/logout', name: 'customer_logout', methods:['POST'])]
    public function CustomerLogout(Request $request, SessionInterface $session): JsonResponse
    {
        $session->invalidate();
        return new JsonResponse(['success'=>'Customer Logged out successfully']);
    }
}
