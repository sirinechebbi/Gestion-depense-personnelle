<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Depense;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr' => ['placeholder' => 'Ex: Courses Carrefour', 'class' => 'form-control'],
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant (DT)',
                'currency' => 'TND',
                'attr' => ['placeholder' => '0.00', 'class' => 'form-control'],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'query_builder' => fn(CategorieRepository $repo) => $repo->createQueryBuilder('c')
                    ->andWhere('c.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('c.nom', 'ASC'),
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'Détails supplémentaires...', 'class' => 'form-control', 'rows' => 3],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Depense::class]);
        $resolver->setRequired('user');
    }
}
