<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateNameType;
use App\Form\ChangePasswordType;
use App\Entity\Photo;
use App\Form\PhotoUploadType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        // Formulaire upload photo
        $photoForm = $this->createForm(PhotoUploadType::class);
        $photoForm->handleRequest($request);

        if ($photoForm->isSubmitted() && $photoForm->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $photoForm->get('photo')->getData();

            $newFilename = uniqid('photo_', true) . '.' . $uploadedFile->guessExtension();

            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/photos';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0775, true);
            }
            $uploadedFile->move($uploadDirectory, $newFilename);

            $photo = new Photo();
            $photo->setNomFichier($newFilename);
            $photo->setCheminFichier('/uploads/photos/' . $newFilename);
            $photo->setDateCreation(new \DateTime());
            $photo->setUtilisateur($user);
            $photo->setValidee(false); 

            $em->persist($photo);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre photo a bien été envoyée et attend maintenant la validation de l’administrateur.'
            );

            return $this->redirectToRoute('app_account');
        }

        return $this->render('security/account.html.twig', [
            'user'         => $user,
            'nameForm'     => $nameForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'photoForm'    => $photoForm->createView(),
            'photos'       => $user->getPhotos(),
            'reservations' => $user->getReservations(),
        ]);
    }

    #[Route('/photo/{id}/delete', name: 'photo_delete', methods: ['POST'])]
    public function deletePhoto(Photo $photo, EntityManagerInterface $em): Response
    {
        // Sécurité : la photo doit appartenir à l'utilisateur connecté
        if ($photo->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette photo.');
        }

        // Suppression du fichier physique sur le disque
        $filePath = $this->getParameter('kernel.project_dir')
            . '/public' . $photo->getCheminFichier();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'La photo a été supprimée.');

        return $this->redirectToRoute('app_account');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
