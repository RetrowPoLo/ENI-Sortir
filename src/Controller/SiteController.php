<?php

namespace App\Controller;

use App\Entity\LocationSite;
use App\Form\GetVilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\LocationSiteType;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'app_site')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // creates a task object and initializes some data for this example
        $ville = new LocationSite();
        $site = new LocationSite();

        $AllVille = $entityManager->getRepository(LocationSite::class)->findAll();

        $formVille = $this->createForm(GetVilleType::class, $ville);
        $form = $this->createForm(LocationSiteType::class, $site);


        $formVille->handleRequest($request);
        if ($formVille->isSubmitted() && $formVille->isValid()) {
            $ville->setName($formVille->get("name")->getData());
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('app_site');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contain = $form->get("name")->getData();
            return $this->redirectToRoute('app_site', ['contain' => $contain]);
        }
        $contain = $request->query->get('contain');
        return $this->render('site/index.html.twig', [
            'form' => $form,
            'formVille' => $formVille,
            'villes' => $AllVille,
            'contain' => $contain,
        ]);
    }
    #[Route('/site/delete/{id}', name: 'app_delete_site', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, LocationSite $locationSite): Response
    {
        $entityManager->remove($locationSite);
        $entityManager->flush();
        return $this->redirectToRoute('app_site');
    }
}
