<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Repository\UnitsRepository;
use App\Entity\Customers;
use App\Repository\WorkersRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Units;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Enum\UnitStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/api')]
class UnitController extends AbstractController
{
    #[Route('/createUnit', name: 'unit_create', methods:['POST', 'GET'])]
    public function createUnit(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, CustomersRepository $customersRepository): JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);
            
            if(!isset($data['name'], $data['description'])){
                return new JsonResponse(['error'=>'Missing required fields']);
            }

            $unit = new Units();
            $created_by = $session->get('user_id');

            if(!$created_by){
                return new JsonResponse(['error'=>'Customer Id is empty...']);
            }
            $unit->setCustomerId($customersRepository->findOneBy(['id'=>$created_by]));
            $unit->setDescription($data['description']);
            $unit->setUnitName($data['name']);
            $unit->setTimestamp(new \DateTime()); 
            $unit->setStatus(UnitStatus::RUNNING);
            $unit->setNotes(null);
            
            if(!isset($unit)){
                return new JsonResponse(['error'=>'Unit object is empty']);
            }

            $entityManager->persist($unit);
            $entityManager->flush();
        }
        catch(\Exception $e){
            return new JsonResponse(['error'=>"error: " . $e->getMessage()]);
        }

        return new JsonResponse(['success'=>'Unit created']);
    }

    #[Route('/getAllCompanyUnits', name: 'unit_show', methods:['GET'])]
    public function getAllCompanyUnits(Request $request, SessionInterface $session, UnitsRepository $unitsRepository, CustomersRepository $customersRepository): JsonResponse{/* FELSÖK */
        try{
                // Hämta units med customer_id = 92
                $id = $session->get('customer_id');
                $customer = $customersRepository->findOneBy(['id'=>$id]);
                
                if (!isset($customer)) {
                    return new JsonResponse(['error' => 'No customer found for company ' . $id]);
                }

                $units = $unitsRepository->findBy(['customer_id' => $customer->getId()]);

                if (empty($units)) {
                    return new JsonResponse(['error' => 'No units found']);
                }

              $unitsArray = array_map(function($unit) {
                    return [
                        'id' => $unit->getId(),
                        'name' => $unit->getUnitName(),
                        'description' => $unit->getDescription(),
                        'customer' => $unit->getCustomerId()->getName(),
                        'status'=>$unit->getStatus(),
                        'timestamp'=>$unit->getTimestamp()->format('Y-m-d H:i'),
                        'notes'=>$unit->getNotes(),
                        'unit_id'=>$unit->getId(),
                        /* 'assignedWorker'=>$unit->getAssignedWorker() */
                    ];
                }, $units); 
                
                return new JsonResponse(['success' => $unitsArray]);
                         
        }catch(\Exception $e){
            return new JsonResponse(['error'=>"error: " . $e->getMessage()]);
        }
        
    }

    #[Route('/getAllCompanyUnits/{id}', name: 'unit_worker_show', methods:['GET'])]
    public function getAllCompanyWorkerUnits(int $id, Request $request, SessionInterface $session, UnitsRepository $unitsRepository, WorkersRepository $workersRepository): JsonResponse{
        try{
                $worker = $workersRepository->findOneBy(['id'=>$id]);
                if (!isset($worker)) {
                    return new JsonResponse(['error' => 'No worker found for id ' . $id]);
                }

                $units = $worker->getUnitIDs()->toArray();
                
                if (empty($units)) {
                    return new JsonResponse(['error' => 'No units found']);
                }

              $unitsArray = array_map(function($unit) {
                    return [
                        'id' => $unit->getId(),
                        'name' => $unit->getUnitName(),
                        'description' => $unit->getDescription(),
                        'customer' => $unit->getCustomerId()->getName(),
                        'status'=>$unit->getStatus(),
                        'timestamp'=>$unit->getTimestamp()->format('Y-m-d H:i'),
                        'notes'=>$unit->getNotes(),
                        'unit_id'=>$unit->getId(),
                       
                    ];
                }, $units); 
                
                return new JsonResponse(['success' => $unitsArray]);
                         
        }catch(\Exception $e){
            return new JsonResponse(['error'=>"error: " . $e->getMessage()]);
        }
        
    }

    #[Route('/unit/{id}', name: 'unit_get', methods:['GET'])]
public function getUnit(int $id, Request $request, UnitsRepository $unitsRepository): JsonResponse {
    $unit = $unitsRepository->findOneBy(['id' => $id]);

    if (!$unit) {
        return new JsonResponse(['error' => 'No unit found.'], 404);
    }

    $unitArr = [
        'name' => $unit->getUnitName(),
        'description' => $unit->getDescription(),
        'customer' => $unit->getCustomerId()->getName(),
        'status' => $unit->getStatus(),
        'timestamp' => $unit->getTimestamp()->format('Y-m-d H:i'),
        'notes' => $unit->getNotes(),
        'unit_id' => $unit->getId(),
    ];

    return new JsonResponse(['success' => $unitArr]);
}

#[Route('/unit/delete/{id}', name: 'unit_delete', methods:['POST'])] //DELETE
public function deleteUnit(int $id, Request $request, UnitsRepository $unitsRepository, EntityManagerInterface $entityManager): JsonResponse {
    if(!$id) return new JsonResponse(['error'=>"No Unit Id Provided"]);
    $unit = $unitsRepository->find($id);

    if(!$unit){
        return new JsonResponse(['error' => 'Unit not found.'], 404);
    }

    $entityManager->remove($unit);
    $entityManager->flush();

    return new JsonResponse(['success'=>'Unit Deleted successfully']);
}

#[Route('/unit/update/{id}', name: 'unit_delete', methods:['PUT'])] 
public function updateUnit(int $id, ValidatorInterface $validator,Request $request, UnitsRepository $unitsRepository, EntityManagerInterface $entityManager): JsonResponse {
    if(!$id) return new JsonResponse(['error'=>"No Unit Id Provided"]);

    $data = json_decode($request->getContent(), true);

    if (empty($data)) {
        return new JsonResponse(['error' => "No changes were made"], 400);
    }

    $unit = $unitsRepository->find($id);

    if(!$unit){
        return new JsonResponse(['error' => 'Unit not found.'], 404);
    }

    $violations = $validator->validate($unit);

    // Om det finns valideringsfel, returnera dem som ett svar
    if (count($violations) > 0) {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return new JsonResponse(['errors' => $errors], 400);
    }

     if (!empty($data['name']) && $data['name'] !== $unit->getUnitName()) {
        $unit->setUnitName($data['name']);
    }

    if (!empty($data['description']) && $data['description'] !== $unit->getDescription()) {
        $unit->setDescription($data['description']);
    }

    if (!empty($data['status'])) {
        $status = strtolower($data['status']); 
        switch ($status) {
            case 'running':
                $unit->setStatus(UnitStatus::RUNNING);
                break;
            case 'stopped':
                $unit->setStatus(UnitStatus::STOPPED);
                break;
            case 'maintenance':
                $unit->setStatus(UnitStatus::MAINTENANCE);
                break;
            case 'error':
                $unit->setStatus(UnitStatus::ERROR);
                break;
            default:
                break;
        }
    }
/* 
    if (!$entityManager->getUnitOfWork()->isScheduledForUpdate($unit)) {
        return new JsonResponse(['error' => "No changes were made"], 400);
    } */

    $entityManager->flush(); //Doctrine's Unit of Work gör att ändringar hittas automatiskt för uniten

    return new JsonResponse(['success'=>'Unit Updated successfully']);
}

}
