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
        if(!isset($userID)){
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
    public function authLicense(Request $request, CustomersRepository $customersRepository) : JsonResponse{
        $session = $request->getSession();
        $userID = $session->get('user_id');
        $role = $session->get('role');
        if(isset($role) && $role === 'ROLE_OWNER'){
            if(!$customersRepository->findOneBy(['id'=>$userID])->isLicenseValid()){
                return new JsonResponse(["error"=>"Company License is not valid. Contact support"]);
            }
        }
        return new JsonResponse(['success'=>'Licensekey is valid']);

        /* FIXA BÄTTRE SEDAN OCH IMPLEMENTERA */

    }




    
}