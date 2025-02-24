<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Repository\UnitsRepository;
use App\Repository\WorkersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Workers;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\PasswordLessService;

#[Route('/api')]
class WorkerController extends AbstractController
{
    #[Route('/registerWorker', name: 'register_worker')]
    public function registerWorker(Request $request, SessionInterface $session, UnitsRepository $unitsRepository, WorkersRepository $workersRepository, EntityManagerInterface $entityManager,CustomersRepository $customersRepository): JsonResponse
    {
        /* HAR GLÖMT EN GREJ: ATT I FORMULÄRET SKA MAN KUNNA TILLDELA VISSA UNITS. UNITS ENTITET HAR EN ADDWORKER */
        $data = json_decode($request->getContent(), true);

       /*  $company_id = $session->get('customer_id');

        if(!$company_id) return new JsonResponse(['error'=>'Company id not found ']); */

        if(!isset($data['worker_name'], $data['worker_tel'], $data['worker_email'], $data['employment_type'], $data['workerUnits'])) {/* , $data['workerCompany'] */
            return new JsonResponse(['error' => 'Missing required fields']);
        }/* DETTA KAN VARA FEL */

        if(!ctype_digit($data['worker_tel'])){ /* FÅR KANSKE ÄNDRA SEN +46 ETC " libphonenumber" biblotek */
            return new JsonResponse(['error' => 'Phonenumber must only contain digits']);
        }

        $existingWorker = $workersRepository->findOneBy(['worker_email'=>$data['worker_email']]);

        if($session->get('role') === "ROLE_OWNER" && $session->get('user_id')){
            $workerCompany = $session->get('user_id');
        }/* DETTA KAN VARA FEL */

        $companyObj = $customersRepository->findOneBy(['id'=>$workerCompany]); /* $data['workerCompany'] */ /* DETTA KAN VARA FEL */

        if($existingWorker) return new JsonResponse(['error'=>'This worker is already registered on your company']);

        $worker = new Workers();
        $worker->setEmploymentType($data['employment_type']);
        $worker->setFullName($data['worker_name']);
        $worker->setPhoneNumber($data['worker_tel']);
        $worker->setRoles(['ROLE_WORKER']);
        $worker->setWorkerEmail($data['worker_email']);
        $worker->addCompanyId( $companyObj);
        
        foreach($data['workerUnits'] as $unitId){ /* DETTA KAN VARA FEL */
            $unit = $unitsRepository->findOneBy(['id'=>$unitId]);
            $unit->addWorker($worker);
        } /* Här läggs works till för units samt i addWorker funktionen samt på workern läggs tilldelade units till */

        if(!isset($worker)) return new JsonResponse(['error'=>'Error during worker registration']);

        $entityManager->persist($worker);
        $entityManager->flush();

        return new JsonResponse(['success'=>"Worker registered successfully"]);
    }

    #[Route('/loginWorker', name: 'login_Worker')]
    public function loginWorker(Request $request, SessionInterface $session, WorkersRepository $workersRepository, EntityManagerInterface $entityManager): JsonResponse{
        $loginData = json_decode($request->getContent(), true);

        if(!isset($loginData['worker_email'])){ 
            return new JsonResponse(['error' => 'Missing required fields']);
        }

        $worker = $workersRepository->findOneBy(['worker_email'=>$loginData['worker_email']]);

        if(!$worker){
            return new JsonResponse(['error'=>'Worker not found']);
        }

        $passwordService = new PasswordLessService($session);

        $response = $passwordService->PasswordLess($loginData['worker_email']);
    
        if(isset($response['error'])){
            return new JsonResponse(['error'=>"Error during passwordless emailing"]);
        }

        if(!$session->get('realPassword') || !$session->get('email')){
            return new JsonResponse(['error'=>'Session is broken...']);
        }

        return new JsonResponse(['success'=>'Email with one-time password sent']);
    }
}
