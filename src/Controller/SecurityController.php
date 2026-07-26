<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $em->persist($user);

            // Créer des catégories par défaut
            $categoriesDefaut = [
                ['nom' => 'Alimentation', 'couleur' => '#EF4444', 'icone' => 'bi-cart3'],
                ['nom' => 'Transport', 'couleur' => '#3B82F6', 'icone' => 'bi-car-front'],
                ['nom' => 'Logement', 'couleur' => '#8B5CF6', 'icone' => 'bi-house'],
                ['nom' => 'Santé', 'couleur' => '#10B981', 'icone' => 'bi-heart-pulse'],
                ['nom' => 'Loisirs', 'couleur' => '#F59E0B', 'icone' => 'bi-controller'],
                ['nom' => 'Habillement', 'couleur' => '#EC4899', 'icone' => 'bi-bag'],
                ['nom' => 'Abonnements', 'couleur' => '#6366F1', 'icone' => 'bi-repeat'],
                ['nom' => 'Autres', 'couleur' => '#6B7280', 'icone' => 'bi-three-dots'],
            ];

            foreach ($categoriesDefaut as $data) {
                $cat = new \App\Entity\Categorie();
                $cat->setNom($data['nom']);
                $cat->setCouleur($data['couleur']);
                $cat->setIcone($data['icone']);
                $cat->setUser($user);
                $em->persist($cat);
            }

            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès ! Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method should never be reached.');
    }
}
