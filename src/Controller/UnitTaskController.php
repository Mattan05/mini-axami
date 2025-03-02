<?php

namespace App\Controller;

use App\Entity\UnitTasks;
use App\Repository\UnitTasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CustomersRepository;
use App\Repository\WorkersRepository;
use App\Repository\UnitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\UnitTaskCategory;
use App\Enum\UnitTaskStatus;

#[Route('/api')]
class UnitTaskController extends AbstractController
{
    #[Route('/createTask', name: 'task_create')]
    public function createTask(Request $request, SessionInterface $session,EntityManagerInterface $entityManagerInterface,CustomersRepository $customersRepository, UnitsRepository $unitsRepository,WorkersRepository $workersRepository): JsonResponse
    {   
        $data = json_decode($request->getContent(), true);

        if(!isset($data['category'], $data['description'], $data['title'], $data['unit_id'], $data['assigned_workers'])){
            return new JsonResponse(['error'=>'Missing required fields']);
        }

        $user_role = (array) $session->get('role');
        
        $user_id = $session->get('user_id');

        $task = new UnitTasks();
        $unit = $unitsRepository->find($data['unit_id']);

        if(in_array('ROLE_OWNER', $user_role)){
            $customer = $customersRepository->find($user_id);
            if(!$customer) return new JsonResponse(['error'=>'Creater (customer) not found']);
            $task->setCreatedBy($customer);
            $task->setStatus(UnitTaskStatus::NOT_STARTED);
        }else if(in_array('ROLE_WORKER', $user_role)){
            $worker = $workersRepository->find($user_id);
            if(!$worker) return new JsonResponse(['error'=>'Creater (worker) not found']);
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
        $unit->addUnitTask($task);

        if($data['assigned_workers']){
           foreach((array) $data['assigned_workers'] as $worker_id) {
                $assigned_worker = $workersRepository->find($worker_id); 
                $task->addAssignedWorker($assigned_worker);
            }
        }
        
       /*  $task->addSolvedBy(null);
        * BEHÖVER REDIGERA addSolvedBy FÖR ATT KUNNA TA EMOT NULL SOM PARAMETER */ 


        $entityManagerInterface->persist($task);
        $entityManagerInterface->flush();

        return new JsonResponse(['success'=>'Created Successfully']);


        /*  'id' => $this->id,
            'timestamp' => $this->timestamp,
            'createdByWorker' => $this->createdByWorker ? $this->createdByWorker->toArray() : null, 
            'createdByCustomer' => $this->createdByCustomer ? $this->createdByCustomer->toArray() : null, 
            'status' => $this->status ? $this->status->value : null, 
            'category' => $this->category ? $this->category->value : null, 
            'unitID' => array_map(fn($unit) => $unit->toArray(), $this->unitID->toArray()), 
            'description' => $this->description,
            'task_title' => $this->task_title,
            'assigned_worker' => array_map(fn($worker) => $worker->toArray(), $this->assigned_worker->toArray()), 
            'notes' => $this->notes,
            'solved_by' => array_map(fn($worker) => $worker->toArray(), $this->solved_by->toArray()),  */
    }
    #[Route('/getUnitTasks/{id}', name:'get_unittasks_forUnit')]
    public function getUnitTasks(int $id, SessionInterface $session, UnitTasksRepository $unitTasksRepository, UnitsRepository $unitsRepository, EntityManagerInterface $entityManager):JsonResponse{
        $unit = $unitsRepository->find($id);
        if(!$unit) return new JsonResponse(['error'=>'Unit not found']);

        $unitTasks = $unitTasksRepository->findBy(['unitID'=>$unit]);

        if(!$unitTasks) return new JsonResponse(['error'=>'Could not find unittasks']);

        $unitTaskArr = array_map(function($unitTask){
            return $unitTask->toArray();
        },$unitTasks);

        return new JsonResponse(['success'=>$unitTaskArr]);
    }

    #[Route('/getUnitTasks', name:'get_unittasks')]
    public function getAllUnitTasks(SessionInterface $session, CustomersRepository $customersRepository,UnitTasksRepository $unitTasksRepository, UnitsRepository $unitsRepository, EntityManagerInterface $entityManager):JsonResponse{
        $customer_id = $session->get('customer_id');

        $customer_id = 92;
    
        if (!$customer_id) {
            return new JsonResponse(['error' => 'Customer_id not found']);
        }

        $customer = $customersRepository->find($customer_id);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found']);
        }

        $units = $unitsRepository->findBy(['customer_id' => $customer->getId()]);/* fd */

        if (!$units) {
            return new JsonResponse(['error' => 'No units found for this customer']);
        }

        $unitTasks = $unitTasksRepository->createQueryBuilder('ut')/* KOLLA IGENOM OCH FÖRSTÅ */
            ->innerJoin('ut.unitID', 'u')
            ->where('u IN (:units)')
            ->setParameter('units', $units)
            ->getQuery()
            ->getResult();

        if (!$unitTasks) {
            return new JsonResponse(['error' => 'No unit tasks found for this company']);
        }

        $unitTaskArr = array_map(fn($unitTask) => $unitTask->toArray(), $unitTasks);

        return new JsonResponse(['success' => $unitTaskArr]);
    }
}
