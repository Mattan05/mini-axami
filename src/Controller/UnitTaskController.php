<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UnitTaskController extends AbstractController
{
    #[Route('/createTask', name: 'task_create')]
    public function createTask($request): JsonResponse
    {   
        $data = json_decode($request->getContent(), true);

        if(!isset($data[''],))

        return new JsonResponse(['success'=>'Created SUccessfully']);

        /* private ?int $id = null;
        private ?\DateTimeInterface $timestamp = null;
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
