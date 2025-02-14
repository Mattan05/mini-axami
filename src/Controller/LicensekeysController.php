<?php

namespace App\Controller;

use App\Entity\Licensekeys;
use App\Form\LicensekeysType;
use App\Repository\LicensekeysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/licensekeys')]
final class LicensekeysController extends AbstractController
{
    #[Route(name: 'app_licensekeys_index', methods: ['GET'])]
    public function index(LicensekeysRepository $licensekeysRepository): JsonResponse
    {
        $licensekeys = $licensekeysRepository->findAll();

        return new JsonResponse($licensekeys);
    }

    #[Route('/{id}', name: 'app_licensekeys_show', methods: ['GET'])]
    public function show(Licensekeys $licensekey): Response
    {
        return $this->render('licensekeys/show.html.twig', [
            'licensekey' => $licensekey,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_licensekeys_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Licensekeys $licensekey, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LicensekeysType::class, $licensekey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_licensekeys_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('licensekeys/edit.html.twig', [
            'licensekey' => $licensekey,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_licensekeys_delete', methods: ['POST'])]
    public function delete(Request $request, Licensekeys $licensekey, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$licensekey->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($licensekey);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_licensekeys_index', [], Response::HTTP_SEE_OTHER);
    }
}
