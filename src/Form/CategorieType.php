<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $icones = [
            'Alimentation' => 'bi-cart3',
            'Transport' => 'bi-car-front',
            'Logement' => 'bi-house',
            'Santé' => 'bi-heart-pulse',
            'Loisirs' => 'bi-controller',
            'Habillement' => 'bi-bag',
            'Abonnements' => 'bi-repeat',
            'Éducation' => 'bi-book',
            'Voyages' => 'bi-airplane',
            'Restaurant' => 'bi-cup-hot',
            'Sport' => 'bi-bicycle',
            'Technologie' => 'bi-laptop',
            'Épargne' => 'bi-piggy-bank',
            'Famille' => 'bi-people',
            'Animaux' => 'bi-heart',
            'Beauté' => 'bi-stars',
            'Cadeaux' => 'bi-gift',
            'Impôts' => 'bi-bank',
            'Assurance' => 'bi-shield-check',
            'Autres' => 'bi-three-dots',
        ];

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => ['placeholder' => 'Ex: Alimentation', 'class' => 'form-control'],
            ])
            ->add('couleur', ColorType::class, [
                'label' => 'Couleur',
                'attr' => ['class' => 'form-control form-control-color'],
            ])
            ->add('icone', ChoiceType::class, [
                'label' => 'Icône',
                'choices' => array_flip($icones),
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Categorie::class]);
    }
}
