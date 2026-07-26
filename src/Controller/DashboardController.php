<?php

namespace App\Controller;

use App\Repository\DepenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(DepenseRepository $depenseRepository): Response
    {
        $user = $this->getUser();
        $annee = (int) date('Y');

        $totalMoisCourant = $depenseRepository->getTotalMoisCourant($user);
        $totalMoisPrecedent = $depenseRepository->getTotalMoisPrecedent($user);
        $evolution = $totalMoisPrecedent > 0
            ? (($totalMoisCourant - $totalMoisPrecedent) / $totalMoisPrecedent) * 100
            : 0;

        $depensesRecentes = $depenseRepository->getDepensesRecentes($user, 8);
        $parCategorie = $depenseRepository->getTotalParCategorie($user,
            new \DateTime('first day of this month'),
            new \DateTime('last day of this month')
        );

        $parMois = $depenseRepository->getTotalParMois($user, $annee);
        $donneesGraphique = array_fill(0, 12, 0);
        foreach ($parMois as $row) {
            $donneesGraphique[(int)$row['mois'] - 1] = (float) $row['total'];
        }

        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        return $this->render('dashboard/index.html.twig', [
            'total_mois_courant' => $totalMoisCourant,
            'total_mois_precedent' => $totalMoisPrecedent,
            'evolution' => $evolution,
            'depenses_recentes' => $depensesRecentes,
            'par_categorie' => $parCategorie,
            'donnees_graphique' => $donneesGraphique,
            'mois_noms' => $moisNoms,
            'annee' => $annee,
        ]);
    }
}
