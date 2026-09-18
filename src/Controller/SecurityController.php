<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateNameType;
use App\Form\ChangePasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Création du formulaire de connexion
        $form = $this->createForm(\App\Form\LoginFormType::class, [
            'email' => $lastUsername,
        ]);

        $form->handleRequest($request);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/account', name: 'app_account')]
    public function account(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // Formulaire nom
        $nameForm = $this->createForm(UpdateNameType::class, $user);
        $nameForm->handleRequest($request);

        if ($nameForm->isSubmitted() && $nameForm->isValid()) {
            $em->flush();
        }

        // Formulaire mot de passe
        $passwordForm = $this->createForm(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $old = $passwordForm->get('oldPassword')->getData();
            $new = $passwordForm->get('newPassword')->getData();

            if ($hasher->isPasswordValid($user, $old)) {
                $user->setMotDePasse($hasher->hashPassword($user, $new));
                $em->flush();
            }
        }

        return $this->render('security/account.html.twig', [
            'user' => $user,
            'nameForm' => $nameForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'reservations' => $user->getReservations(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
