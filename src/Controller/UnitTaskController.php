<?php

namespace App\Controller;

use App\Entity\UnitTasks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CustomersRepository;
use App\Repository\WorkersRepository;
use App\Repository\UnitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\UnitTaskCategory;
use App\Enum\UnitTaskStatus;

class UnitTaskController extends AbstractController
{
    #[Route('/createTask', name: 'task_create')]
    public function createTask(Request $request, EntityManagerInterface $entityManagerInterface,CustomersRepository $customersRepository, UnitsRepository $unitsRepository,WorkersRepository $workersRepository): JsonResponse
    {   
        $data = json_decode($request->getContent(), true);

        //skickar med flagga för worker eller customer som skapat
        if(!isset($data['user_type'], $data['user_id'], $data['category'], $data['description'], $data['title'], $data['unit_id'])){/* ,$data['assigned_workers'] */
            return new JsonResponse(['error'=>'Missing required fields']);
        }

        $task = new UnitTasks();
        $unit = $unitsRepository->findOneBy(['id'=>$data['unit_id']]);

        if($data['user_type'] === 'customer'){
            $customer = $customersRepository->findOneBy(['id'=>$data['user_id']]);
            $task->setCreatedBy($customer);
            $task->setStatus(UnitTaskStatus::NOT_STARTED);
        }else if($data['user_type'] === 'worker'){
            $worker = $workersRepository->findOneBy(['id'=>$data['user_id']]);
            $task->setCreatedBy($worker);
            $task->setStatus(UnitTaskStatus::PENDING); /* PÅ NÅGOT SÄTT GÖR DET ANNORLUNDA OM EN WORKER SKAPAR. CUSTOMER BEHÖVER ACCEPTERA */
        }

        $category_res = $task->setCategory(UnitTaskCategory::tryFrom($data['category']));
        if($category_res === null){
            return new jsonResponse(['error'=>'Wrong Category Input']); /* tryFrom retunerar null om det inte finns någon match till enumet - kan ta bort sen, bara för testning */
        }

        $task->setDescription($data['description']);
        $task->setTaskTitle($data['title']);
        $task->setNotes(null);
        $task->setTimestamp(new \DateTime()); 
        $task->addUnitID($unit);

        if($data['assigned_workers']){
           foreach($data['assigned_workers'] as $worker_id) {
                $assigned_worker = $workersRepository->findOneBy(['id'=>$worker_id]); 
                $task->addAssignedWorker($assigned_worker);
            }
        }
        
       /*  $task->addSolvedBy(null);
        * BEHÖVER REDIGERA addSolvedBy FÖR ATT KUNNA TA EMOT NULL SOM PARAMETER */ 

       

        /* await här */$unit->addUnitTask($task, $data['user_type']);

        $entityManagerInterface->persist($task);
        $entityManagerInterface->flush();

        return new JsonResponse(['success'=>'Created Successfully']);

        /* private ?int $id = null;
        private ?\DateTimeInterface $timestamp = null;      
    private ?Workers $created_by_worker = null;
    private ?Customers $created_by_customer = null;

        private ?Workers $created_by = null; BORDE TA BORT DENNA. TANKEN ÄR ATT CUSTOMER GÖR TASKS ÅT WORKER eller... båda ska kanske kunna skapa fast om en worker skapar behöver den vara
        bekräftad av admin innan via email. ändra created_by till bara ett id eftersom det kan vara både och eller ha så det både kan vara worker och customer
        private ?string $status = null;
        private ?string $category = null;
        private Collection $unitID;
        private ?string $description = null;
        private ?string $task_title = null;
        private Collection $assigned_worker;
        private ?string $notes = null;
        private Collection $solved_by; */


        /* !!!Lägga till workers som kopplas till företag eller enskild unit. LÄGG TILL när man registerar att tilldela vissa maskiner. ist bara kunna se dem sen även en "all" variant
        så kan de olika workers ha olika roller för olika permissions. ROLE_WORKERALL ROLE_WORKERONE ex
        */
    }
}
