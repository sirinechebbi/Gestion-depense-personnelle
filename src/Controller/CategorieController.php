<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $repo): Response
    {
        $categories = $repo->findByUser($this->getUser());
        return $this->render('categorie/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/nouvelle', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setUser($this->getUser());
            $em->persist($categorie);
            $em->flush();
            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('categorie/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/modifier', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        if ($categorie->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée.');
            return $this->redirectToRoute('app_categorie_index');
        }

        return $this->render('categorie/edit.html.twig', ['form' => $form, 'categorie' => $categorie]);
    }

    #[Route('/{id}/supprimer', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        if ($categorie->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->getPayload()->getString('_token'))) {
            if ($categorie->getDepenses()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer une catégorie qui contient des dépenses.');
            } else {
                $em->remove($categorie);
                $em->flush();
                $this->addFlash('success', 'Catégorie supprimée.');
            }
        }

        return $this->redirectToRoute('app_categorie_index');
    }
}
