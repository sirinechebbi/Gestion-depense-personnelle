<?php

namespace App\Repository;

use App\Entity\Depense;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    public function findByUserWithFilters(User $user, array $filtres = []): array
    {
        $qb = $this->createQueryBuilder('d')
            ->join('d.categorie', 'c')
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->orderBy('d.date', 'DESC')
            ->addOrderBy('d.creeLe', 'DESC');

        if (!empty($filtres['categorie'])) {
            $qb->andWhere('d.categorie = :categorie')
               ->setParameter('categorie', $filtres['categorie']);
        }

        if (!empty($filtres['date_debut'])) {
            $qb->andWhere('d.date >= :date_debut')
               ->setParameter('date_debut', $filtres['date_debut']);
        }

        if (!empty($filtres['date_fin'])) {
            $qb->andWhere('d.date <= :date_fin')
               ->setParameter('date_fin', $filtres['date_fin']);
        }

        if (!empty($filtres['montant_min'])) {
            $qb->andWhere('d.montant >= :montant_min')
               ->setParameter('montant_min', $filtres['montant_min']);
        }

        if (!empty($filtres['montant_max'])) {
            $qb->andWhere('d.montant <= :montant_max')
               ->setParameter('montant_max', $filtres['montant_max']);
        }

        if (!empty($filtres['recherche'])) {
            $qb->andWhere('d.libelle LIKE :recherche OR d.description LIKE :recherche')
               ->setParameter('recherche', '%' . $filtres['recherche'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function getTotalParMois(User $user, int $annee): array
    {
        $debut = new \DateTime("$annee-01-01");
        $fin = new \DateTime("$annee-12-31");

        $depenses = $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->andWhere('d.date >= :debut')
            ->andWhere('d.date <= :fin')
            ->setParameter('user', $user)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult();

        $totaux = [];
        foreach ($depenses as $d) {
            $mois = (int) $d->getDate()->format('n');
            $totaux[$mois] = ($totaux[$mois] ?? 0) + (float) $d->getMontant();
        }

        $result = [];
        foreach ($totaux as $mois => $total) {
            $result[] = ['mois' => $mois, 'total' => $total];
        }
        usort($result, fn($a, $b) => $a['mois'] <=> $b['mois']);

        return $result;
    }

    public function getTotalParCategorie(User $user, ?\DateTimeInterface $debut = null, ?\DateTimeInterface $fin = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('c.nom, c.couleur, SUM(d.montant) as total')
            ->join('d.categorie', 'c')
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->groupBy('c.id')
            ->orderBy('total', 'DESC');

        if ($debut) {
            $qb->andWhere('d.date >= :debut')->setParameter('debut', $debut);
        }
        if ($fin) {
            $qb->andWhere('d.date <= :fin')->setParameter('fin', $fin);
        }

        return $qb->getQuery()->getResult();
    }

    public function getTotalMoisCourant(User $user): float
    {
        $now = new \DateTime();
        $debut = new \DateTime('first day of this month');
        $fin = new \DateTime('last day of this month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.user = :user')
            ->andWhere('d.date >= :debut')
            ->andWhere('d.date <= :fin')
            ->setParameter('user', $user)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getTotalMoisPrecedent(User $user): float
    {
        $debut = new \DateTime('first day of last month');
        $fin = new \DateTime('last day of last month');

        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.user = :user')
            ->andWhere('d.date >= :debut')
            ->andWhere('d.date <= :fin')
            ->setParameter('user', $user)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getDepensesRecentes(User $user, int $limite = 5): array
    {
        return $this->createQueryBuilder('d')
            ->join('d.categorie', 'c')
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->orderBy('d.date', 'DESC')
            ->addOrderBy('d.creeLe', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }
}
