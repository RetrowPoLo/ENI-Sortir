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

#[Route('/admin', name: 'app_')]
class SiteController extends AbstractController
{
    #[Route('/sites', name: 'site')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
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

			// Redirect to the site page
            return $this->redirectToRoute('app_site');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contain = $form->get("name")->getData();

			// Redirect to the site page
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
    #[Route('/site/supprimer/{id}', name: 'site_delete', requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $entityManager, LocationSite $locationSite): Response
    {
		// Delete the location site and redirect to the site page
        $entityManager->remove($locationSite);
        $entityManager->flush();

        return $this->redirectToRoute('app_site');
    }
}
