<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LicensekeysRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomersRepository;
use App\Entity\Customers;
use App\Entity\Licensekeys;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Controller\LicensekeysController;
use App\Services\LicenseKeyService;
use DateTime;
use App\Services\EmailService;



#[Route('/api')]
class RegistrationController extends AbstractController
{
    
    #[Route('/companyRegistration', name: 'company_registration', methods: ['POST'])]
    public function CompanyRegistration(LicenseKeyService $licenseKeyService, LicensekeysRepository $licensekeysRepository, Request $request, CustomersRepository $customerRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        /* if (!$session->get('user_id')) {
            return new JsonResponse(["error" => "Unauthorized access"]);
        } */
        try{
            $data = json_decode($request->getContent(), true);
        
            if (!isset($data['name'], $data['identificationNumber'], $data['companyEmail'], $data['customerType'])) { /* $data['validUntil'],  */
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            if (strlen($data['identificationNumber']) != 10 && strlen($data['identificationNumber']) != 12) {
                return new JsonResponse(['error' => 'Identification number must be 10 or 12 digits']);
            }

            if(!ctype_digit($data['identificationNumber'])){
                return new JsonResponse(['error' => 'Identificationnumber must only contain digits']);
            }

            $existingCustomer = $customerRepository->findOneBy(['customer_email' => $data['companyEmail']]);
            $existingIdentification =  $customerRepository->findOneBy(['identification_number' => $data['identificationNumber']]);
            if ($existingCustomer) {
                return new JsonResponse(['error' => 'Email is already registered']);
            }
            if($existingIdentification){
                return new JsonResponse(['error' => 'Identification number is already registered']);
            }

            $isPayed = true; /* IMPLEMENTERA STRIPE PAYMENTS WEBHOOKS CLI*/
            if($isPayed === false){
                return new JsonResponse(["error"=>"Payment Error"]);
            }else{
                $customer = new Customers();
                $customer->setIdentificationNumber($data['identificationNumber']);
                $customer->setCustomerEmail($data['companyEmail']);
                $customer->setCustomerType($data['customerType']);
                $customer->setLicenseValid(false);/* true / false */
                $customer->setRoles(["ROLE_OWNER"]);
                $customer->setName($data['name']);

                $validUntil = new DateTime();
                $validUntil->modify('+1 year');
                
                $licenseKey = $licenseKeyService->generateLicense($validUntil, $customer);
            
                $customer->setLicenseKey($licenseKey); 

                if(!isset($customer)){
                    return new JsonResponse(["error"=>"Error during customer registration..."]);  
                }

                $mailFunc = new EmailService();
                $subject = "Your License Key";
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
                            .license-key {
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
                            <h2>Your License Key</h2>
                            <p>Hello ' . htmlspecialchars($to) . ',</p>
                            <p>Thank you for your purchase. Here is your license key:</p>
                            <div class="license-key">' . htmlspecialchars($licenseKey->getLicenseKey()) . '</div>
                            <p>If you have any questions, feel free to contact us.</p>
                            <p class="footer">© ' . date("Y") . ' Mini-Axami. All rights reserved.</p>
                        </div>
                    </body>
                    </html>
                ';

                $response = $mailFunc->EmailService($data['companyEmail'], $subject, $content, $licenseKey);
                if(isset($response['error'])){
                    return new JsonResponse($response);
                }
                
                $entityManager->persist($customer);
                $entityManager->flush();
           
                return new JsonResponse($response);
            }
        }
        catch(\Exception $e){
            return new JsonResponse(['error'=>"error: " . $e]);
        }
        
    }
    #[Route('/licenseRegistration', name: 'license_registration', methods: ['POST'])]
    public function LicensekeyRegistration(CustomersRepository $customerRepository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try{
           /*  $session = $request->getSession(); */
            /* if (!$session->get('user_id')) {
                return new JsonResponse(["error" => "Unauthorized access"]);
            } */

            $data = json_decode($request->getContent(), true);

            if(!isset($data['licenseKey'], $data['identificationNumber'])){
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            if(!ctype_digit($data['identificationNumber'])){
                return new JsonResponse(['error' => 'Identificationnumber must only contain digits']);
            }
           
            $customer = $customerRepository->findOneBy(['identification_number'=>$data['identificationNumber']]);
            $licenseKey = $customer->getLicenseKey()->getLicenseKey();

            if (!$licenseKey) {
                return new JsonResponse(['error' => 'Customer has no registered license key'], Response::HTTP_BAD_REQUEST);
            }

            if(!hash_equals($licenseKey, $data['licenseKey'])){ /* Skyddar mot timing attack */
                return new JsonResponse(['error'=>"Invalid Licensekey... Please try again"]);
            }

            $licensekeyObject = $customer->getLicenseKey();
            $customer->setLicenseValid(true);
            $licensekeyObject->setIsActive(true);

            $entityManager->persist($customer);
            $entityManager->persist($licensekeyObject);
            $entityManager->flush();

/*             $session->set("user_id", $customer->getId());
            $session->set("role", $customer->getRoles());
            $session->set("name", $customer->getName()); */


            return new JsonResponse(["success"=>"Company Active"]);
        }
        catch(\Exception $e){
            return new JsonResponse(['error'=>"error: " . $e]);
        }
    }

    #[Route('/customer/{id}', name: 'customer_get', methods:['GET'])]
public function getUnit(int $id, Request $request, CustomersRepository $customersRepository): JsonResponse {
    $customer = $customersRepository->findOneBy(['id' => $id]);

    if (!$customer) {
        return new JsonResponse(['error' => 'No customer found.'], 404);
    }

    $customerArr = [
        'name' => $customer->getName(),
        'email' => $customer->getCustomerEmail(),
        'identificationNumber' => $customer->getIdentificationNumber(),
        'roles' => $customer->getRoles(),
        'timestamp' => $customer->getTimestamp()->format('Y-m-d H:i'),
        'id' => $customer->getId(),
        'customerType' => $customer->getCustomerType(),
    ];

    return new JsonResponse(['success' => $customerArr]);
}



   
}
