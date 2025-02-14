<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CustomersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class SessionController extends AbstractController{
    #[Route('/auth', name: 'session_auth', methods: ['GET'])] 
    public function auth(Request $request, CustomersRepository $customersRepository) : JsonResponse{
        $session = $request->getSession();
        $userID = $session->get('user_id');
        $role = $session->get('role');
        $name = $session->get('name');
        if(!isset($userID)){
            return new JsonResponse(["error"=>"Unauthorized Access"]);
        } 
        if(!$customersRepository->findOneBy(['id'=>$userID])->isLicenseValid()){
            return new JsonResponse(["error"=>"Company License is not valid. Contact support"]);
        }
            return new JsonResponse(["success"=>['user_id'=>$userID, 'role'=>$role, 'name'=>$name] ]);
    }
}