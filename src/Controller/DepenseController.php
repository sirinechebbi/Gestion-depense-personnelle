<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Form\DepenseType;
use App\Form\FiltreDepenseType;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/depenses')]
class DepenseController extends AbstractController
{
    #[Route('', name: 'app_depense_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DepenseRepository $depenseRepository): Response
    {
        $user = $this->getUser();
        $filtres = [];

        $formFiltre = $this->createForm(FiltreDepenseType::class, null, ['user' => $user]);
        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {
            $data = $formFiltre->getData();
            if ($data['categorie']) $filtres['categorie'] = $data['categorie'];
            if ($data['date_debut']) $filtres['date_debut'] = $data['date_debut'];
            if ($data['date_fin']) $filtres['date_fin'] = $data['date_fin'];
            if ($data['montant_min']) $filtres['montant_min'] = $data['montant_min'];
            if ($data['montant_max']) $filtres['montant_max'] = $data['montant_max'];
            if ($data['recherche']) $filtres['recherche'] = $data['recherche'];
        }

        $depenses = $depenseRepository->findByUserWithFilters($user, $filtres);
        $total = array_sum(array_map(fn($d) => (float)$d->getMontant(), $depenses));

        return $this->render('depense/index.html.twig', [
            'depenses' => $depenses,
            'form_filtre' => $formFiltre,
            'total' => $total,
        ]);
    }

    #[Route('/nouvelle', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $depense = new Depense();
        $depense->setDate(new \DateTime());

        $form = $this->createForm(DepenseType::class, $depense, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depense->setUser($user);
            $em->persist($depense);
            $em->flush();

            $this->addFlash('success', 'Dépense ajoutée avec succès.');
            return $this->redirectToRoute('app_depense_index');
        }

        return $this->render('depense/new.html.twig', [
            'form' => $form,
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($depense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(DepenseType::class, $depense, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Dépense modifiée avec succès.');
            return $this->redirectToRoute('app_depense_index');
        }

        return $this->render('depense/edit.html.twig', [
            'form' => $form,
            'depense' => $depense,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, Depense $depense, EntityManagerInterface $em): Response
    {
        if ($depense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$depense->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($depense);
            $em->flush();
            $this->addFlash('success', 'Dépense supprimée.');
        }

        return $this->redirectToRoute('app_depense_index');
    }
}
