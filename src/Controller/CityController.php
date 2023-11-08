<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CitySearchType;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route('/villes/gerer', name: 'app_city')]
    public function index(CityRepository $cityRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $city = $form->getData();
            $entityManager->persist($city);
            $entityManager->flush();
            return $this->redirectToRoute('app_city');
        }

        $formSearch = $this->createForm(CitySearchType::class, $city);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $city = $formSearch->getData();
            $query = $city->getName();
            $cities = $cityRepository->findBy(
                ['name' => $query],
            );
        }
        else{
            $cities = $cityRepository->findAll();
        }

        return $this->render('city/cities.html.twig', [
            'cities' => $cities,
            'form' => $form,
            'formSearch' => $formSearch
        ]);
    }

    #[Route('/villes/modifier/{id}', name: 'app_city_edit', requirements: ['id' => '\d+'])]
    public function editCity(CityRepository $cityRepository, Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cityEdited = $form->getData();
            $entityManager->persist($cityEdited);
            $entityManager->flush();
            return $this->redirectToRoute('app_city');
        }
        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    #[Route('/villes/supprimer/{id}', name: 'app_city_delete', requirements: ['id' => '\d+'])]
    public function deleteCity(CityRepository $cityRepository, Request $request, EntityManagerInterface $em, int $id): Response
    {
        $city = $cityRepository->find($id);
        $em->remove($city);
        $em->flush();
        return $this->redirectToRoute('app_city');
    }

}
