<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        
        if(!$userID){
            return new JsonResponse(["error"=>"Unauthorized Access"]);
        } 
            return new JsonResponse(["success"=>['user_id'=>$userID, 'role'=>$role, 'name'=>$name] ]);
    }

    #[Route('/logout', name: 'logout', methods:['POST'])]
    public function logout(Request $request, SessionInterface $session): JsonResponse
    {
        $session->invalidate();
        if(!empty($session->get('user_id'))){
            return new JsonResponse(['error'=>'Logout failed, check session']);
        }
        return new JsonResponse(['success'=>'Logged out successfully']);
    }
    
    #[Route('/authLicense', name: 'license_auth', methods: ['GET'])] 
    public function authLicense(Request $request, CustomersRepository $customersRepository): JsonResponse
    {
        $session = $request->getSession();
        $customerId = $session->get('customer_id');
      /*   $role = $session->get('role'); */

        if (!$customerId) {
            return new JsonResponse(["error" => "Unauthorized Access"], 401);
        }

        $customer = $customersRepository->find($customerId);

        if (!$customer) {
            return new JsonResponse(["error" => "Customer not found"], 404);
        }

        if (/* $role === 'ROLE_OWNER' &&  */!$customer->isLicenseValid()) {
            return new JsonResponse(["errorLicense" => "License is not valid"]);
        }

        return new JsonResponse(['success' => true]);
    }




    
}