<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityForm;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/city')]
final class CityController extends AbstractController
{
    #[Route(name: 'app_city_index', methods: ['GET'])]
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CityForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $country = $form->get('country')->getData();
            $active = $form->get('active')->getData();

            $firstCity = trim($form->get('name')->getData());

            $cityNames = $request->request->all('cities');

            $allCities = [];
            if ($firstCity !== '') {
                $allCities[] = $firstCity;
            }
            foreach ($cityNames as $name) {
                $name = trim($name);
                if ($name !== '') {
                    $allCities[] = $name;
                }
            }

            if (count($allCities) === 0) {
                $form->addError(new FormError('Please add at least one city.'));
            } else {

                foreach ($allCities as $cityName) {
                    $city = new City();
                    $city->setName($cityName);
                    $city->setCountry($country);
                    $city->setActive($active);
                    $city->setIsDeleted(false);
                    $entityManager->persist($city);
                }
                $entityManager->flush();

                $this->addFlash('success', 'Cities added successfully.');

                return $this->redirectToRoute('app_city_index');
            }
        }

        return $this->render('city/new.html.twig', [
            'form' => $form->createView(),
        ]);
//        $city = new City();
//        $form = $this->createForm(CityForm::class, $city);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($city);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('city/new.html.twig', [
//            'city' => $city,
//            'form' => $form,
//        ]);
    }

    #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CityForm::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
