<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


final class ApiController extends AbstractController
{
    /* #[Route('/{reactRouting}', name: 'app_react', requirements: ['reactRouting' => '^(?!api).*'], defaults: ['reactRouting' => null])]
    public function getData(): Response
    {
        return new Response(file_get_contents('static.html'));
    } */

    #[Route("/{reactRouting}", name: "react_app", requirements: ["reactRouting" => "^(?!api).*"], defaults: ["reactRouting" => null])]
    public function index(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/static.html';

        if (!file_exists($filePath)) {
            return new JsonResponse(['error'=>'Filen static.html hittades inte på ' . $filePath]);
        }

        return new Response(file_get_contents($filePath));
    }
}
