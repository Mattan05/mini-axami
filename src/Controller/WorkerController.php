<?php

namespace App\Controller;

use App\Repository\WorkersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Workers;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\PasswordLessService;

final class WorkerController extends AbstractController
{
    #[Route('/registerWorker', name: 'register_worker')]
    public function registerWorker(Request $request, SessionInterface $session, WorkersRepository $workersRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $company_id = $session->get('user_id');

        if(!$company_id) return new JsonResponse(['error'=>'Company id not found ']);

        if(!isset($data['worker_name'],$data['worker_tel'], $data['worker_email'], $data['employment_type'] )) {
            return new JsonResponse(['error' => 'Missing required fields']);
        }

        if(!ctype_digit($data['worker_tel'])){ /* FÅR KANSKE ÄNDRA SEN +46 ETC */
            return new JsonResponse(['error' => 'Phonenumber must only contain digits']);
        }

        $existingWorker = $workersRepository->findOneBy(['worker_email'=>$data['worker_email']]);

        if($existingWorker) return new JsonResponse(['error'=>'This worker is already registered on your company']);

        $worker = new Workers();
        $worker->setEmploymentType($data['employment_type']);
        $worker->setFullName($data['worker_name']);
        $worker->setPhoneNumber($data['worker_tel']);
        $worker->setRoles(['ROLE_WORKER']);
        $worker->setWorkerEmail($data['worker_email']);
        $worker->addCompanyId($company_id);

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
