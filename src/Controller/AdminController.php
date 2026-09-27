<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\PhotoRepository;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(ReservationRepository $reservationRepo, PhotoRepository $photoRepo): Response
    {
        $reservations = $reservationRepo->createQueryBuilder('r')
            ->where('r.datetimeReservation > CURRENT_TIMESTAMP()')
            ->orderBy('r.datetimeReservation', 'ASC')
            ->getQuery()
            ->getResult();

        $photosEnAttente = $photoRepo->findBy(
            ['validee' => false],
            ['dateCreation' => 'ASC']
        );

        $photosValidees = $photoRepo->findBy(
            ['validee' => true],
            ['dateCreation' => 'DESC']
        );

        return $this->render('admin/dashboard.html.twig', [
            'reservations' => $reservations,
            'photosEnAttente' => $photosEnAttente,
            'photosValidees'  => $photosValidees,
        ]);
    }

    #[Route('/admin/reservation/{id}/delete', name: 'admin_delete_reservation')]
        public function deleteReservation(Reservation $reservation, EntityManagerInterface $em): Response
        {
            $em->remove($reservation);
            $em->flush();

            return $this->redirectToRoute('admin_dashboard');
        }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(UtilisateurRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/promote', name: 'admin_promote_user')]
    public function promoteUser(Utilisateur $user, EntityManagerInterface $em): Response
    {
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/user/{id}/demote', name: 'admin_demote_user')]
    public function demoteUser(Utilisateur $user, EntityManagerInterface $em): Response
    {
        $user->setRoles(['ROLE_USER']);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/photo/{id}/validate', name: 'admin_validate_photo', methods: ['POST'])]
    public function validatePhoto(Photo $photo, EntityManagerInterface $em): Response
    {
        $photo->setValidee(true);
        $em->flush();

        $this->addFlash('success', 'La photo a été validée.');

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/photo/{id}/reject', name: 'admin_reject_photo', methods: ['POST'])]
    public function rejectPhoto(Photo $photo, EntityManagerInterface $em): Response
    {
        $this->removePhotoFile($photo);
        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'La photo a été refusée et supprimée.');

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/photo/{id}/delete', name: 'admin_delete_photo', methods: ['POST'])]
    public function deletePhoto(Photo $photo, EntityManagerInterface $em): Response
    {
        $this->removePhotoFile($photo);
        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'La photo a été supprimée.');

        return $this->redirectToRoute('admin_dashboard');
    }

    private function removePhotoFile(Photo $photo): void
    {
        $filePath = $this->getParameter('kernel.project_dir')
            . '/public' . $photo->getCheminFichier();

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}