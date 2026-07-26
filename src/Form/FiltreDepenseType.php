<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreDepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('recherche', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher...', 'class' => 'form-control'],
            ])
            ->add('categorie', EntityType::class, [
                'label' => false,
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Toutes les catégories',
                'attr' => ['class' => 'form-select'],
                'query_builder' => fn(CategorieRepository $repo) => $repo->createQueryBuilder('c')
                    ->andWhere('c.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('c.nom', 'ASC'),
            ])
            ->add('date_debut', DateType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('date_fin', DateType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('montant_min', MoneyType::class, [
                'label' => false,
                'required' => false,
                'currency' => 'TND',
                'attr' => ['placeholder' => 'Montant min', 'class' => 'form-control'],
            ])
            ->add('montant_max', MoneyType::class, [
                'label' => false,
                'required' => false,
                'currency' => 'TND',
                'attr' => ['placeholder' => 'Montant max', 'class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
        $resolver->setRequired('user');
    }

    public function getBlockPrefix(): string { return ''; }
}
