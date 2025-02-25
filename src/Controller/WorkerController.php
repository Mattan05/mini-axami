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
        $data = json_decode($request->getContent(), true);
/* KOLLA IFALL EMAILEN REDAN FINNS REGISTRERAD FÖÖR DEN SPECIFIKA FÖRETAGET! */
/* NÄR WORKER SKA LOGGA IN FÅR FÖRST BESTÄMMA FÖRETAG ATT LOGGA IN FÖR... */

        if(!isset($data['worker_name'], $data['worker_tel'], $data['worker_email'], $data['employment_type'], $data['workerUnits'])) {/* , $data['workerCompany'] */
            return new JsonResponse(['error' => 'Missing required fields']);
        }

        if(!ctype_digit($data['worker_tel'])){ /* FÅR KANSKE ÄNDRA SEN +46 ETC " libphonenumber" biblotek */
            return new JsonResponse(['error' => 'Phonenumber must only contain digits']);
        }

        $existingWorker = $workersRepository->findOneBy(['worker_email'=>$data['worker_email']]);
        if($existingWorker) return new JsonResponse(['error'=>'This worker is already registered on your company']);

        if ($session->has('user_id') && in_array("ROLE_OWNER", $session->get('role', []))) {
            $workerCompany = $session->get('user_id');
        }else{
            return new JsonResponse(['error'=>'Company Not Found in session...']);
        }

        $companyObj = $customersRepository->findOneBy(['id'=>$workerCompany]); /* $data['workerCompany'] */ 
    
        $worker = new Workers();
        $worker->setEmploymentType($data['employment_type']);
        $worker->setFullName($data['worker_name']);
        $worker->setPhoneNumber($data['worker_tel']);
        $worker->setRoles(['ROLE_WORKER']);
        $worker->setWorkerEmail($data['worker_email']);
        $worker->addCompanyId( $companyObj);
        
        foreach($data['workerUnits'] as $unitId){
            $unit = $unitsRepository->findOneBy(['id' => $unitId]);
        
            if (!$unit) {
                return new JsonResponse(['error' => "Unit with ID $unitId not found"]);
            }
        
            $unit->addWorker($worker);
        }
         /* Här läggs works till för units samt i addWorker funktionen samt på workern läggs tilldelade units till */

        if(!isset($worker)) return new JsonResponse(['error'=>'Error during worker registration']);

        $entityManager->persist($worker);
        $entityManager->flush();

        return new JsonResponse(['success'=>"Worker registered successfully"]);
    }

    #[Route('/getAllCompanyWorkers', name: 'get_workers', methods:['GET'])]
    public function getAllCompanyWorkers(Request $request, SessionInterface $session, CustomersRepository $customersRepository): JsonResponse
    {
        $companyId = $session->get('customer_id');

        $company = $customersRepository->findOneBy(['id'=>$companyId]);

        if(!$company) return new JsonResponse(['error'=>'Current company not found']);

        $workers = $company->getWorkers()->toArray();
        if(!$workers) return new JsonResponse(['error'=>'No workers found']);

        $workerArr = array_map(function($worker) {
            return [
                'id' => $worker->getId(),
                'name' => $worker->getFullName(),
                'email' => $worker->getWorkerEmail(),
                'roles' => $worker->getRoles(),
                'phoneNmr' => $worker->getPhoneNumber(),
                'employmentType'=>$worker->getEmploymentType(),
                'units' => array_map(function($unit) { 
            return [
                'id' => $unit->getId(),
                'name' => $unit->getUnitName(),
            ];
        }, $worker->getUnitIds()->toArray()),
               /*  'unitTasks'=>array_map(function($task){
                    return [
                        'id'=>$task->getId(),
                        'name'=>$task->getTaskTitle()
                    ];

                },$worker->getUnitTasks()->toArray()) */ /* LÄGG TILL SEDAN EFTER ATT JAG LAGT TÍLL UNITTASKFUNKTIONALITET */
            ];
        }, $workers);
      
        /* lägg till getSolvedUnitTasks senare */
        /* 'timestamp'=>$worker->getTimestamp()->format('Y-m-d'), lägg till en sådan här. worker since: */
        
        return new JsonResponse(['success'=>$workerArr]);
    }

    #[Route('/updateWorker/{id}', name: 'login_Worker', methods: ['POST'])]
    public function updateWorker(int $id, Request $request, UnitsRepository $unitsRepository, WorkersRepository $workersRepository, EntityManagerInterface $entityManager): JsonResponse{
        $data = json_decode($request->getContent(), true);
        
        $worker = $workersRepository->find($id);
        if (!$worker) return new JsonResponse(['error' => 'Worker not found'], 404);
        
        if (isset($data['newName'])) {
            $worker->setFullName($data['newName']);
        }
        if (isset($data['newEmail'])) {
            $worker->setWorkerEmail($data['newEmail']);
        }
        if (isset($data['newPhoneNmr'])) {
            $worker->setPhoneNumber($data['newPhoneNmr']);
        }
        if (isset($data['newEmploymentType'])) {
            $worker->setEmploymentType($data['newEmploymentType']);  
        }
        if (isset($data['newUnitIds'])) {
            foreach($data['newUnitIds'] as $unitId){
                $unit = $unitsRepository->findBy(['id' => $unitId]);
                $worker->addUnitID($unit);
            }
        }

        $entityManager->persist($worker);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Worker updated successfully']);
    }

    #[Route('/delete/{id}', name: 'login_Worker', methods: ['POST'])]
    public function deleteWorker(int $id, Request $request, UnitsRepository $unitsRepository, WorkersRepository $workersRepository, EntityManagerInterface $entityManager): JsonResponse{
        /* AVSKEDA sparka knapp etc->är du säker  på detta... FRÅGA */
        return new JsonResponse(['success' => 'Worker deleted successfully']);
    }

    /* ATT GÖRA:
        *Lös crud för worker
        *lös så att customer kan se alla workers (read)
        *lös så att workern OCH customer kan update worker (eller kanske bara customer hmm)
        *CRUD FÖR CUSTOMER update, delete, osv osv.
    */

    #[Route('/loginWorker', name: 'login_Worker')]
    public function loginWorker(Request $request, SessionInterface $session, CustomersRepository $customersRepository, WorkersRepository $workersRepository, EntityManagerInterface $entityManager): JsonResponse{
        $loginData = json_decode($request->getContent(), true);

        if(!isset($loginData['worker_email'], $loginData['company_choice'])){ 
            return new JsonResponse(['error' => 'Missing required fields']);
        }
         
       /*  $worker = $workersRepository->findOneBy(['worker_email'=>$loginData['worker_email'] && 'company_id']); */ /* && company ==  */
        $company = $customersRepository->findOneBy(['id'=> $loginData['company_choice']]);

        if(!$company) return new JsonResponse(['error'=>"Company object not found for the given id"]);

        $worker = $workersRepository->findOneBy(['worker_email' => $loginData['worker_email']]);

        if (!$worker || !$worker->getCompanyId()->contains($company)) {
            return new JsonResponse(['error' => 'Worker not found for the given email and company'], 404);
        }

        $passwordService = new PasswordLessService($session);

        $response = $passwordService->PasswordLess($loginData['worker_email']);
    
        if(isset($response['error'])){
            return new JsonResponse(['error'=>"Error during passwordless emailing"]);
        }

        if(!$session->get('realPassword') || !$session->get('email')){
            return new JsonResponse(['error'=>'Session is broken...']);
        }

        return new JsonResponse(['success'=>'Email with one-time password sent', 'companyId'=>$company->getId()]);
    }
}
