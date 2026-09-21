<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(ReservationRepository $reservationRepo): Response
    {
        $reservations = $reservationRepo->createQueryBuilder('r')
            ->where('r.datetimeReservation > CURRENT_TIMESTAMP()')
            ->orderBy('r.datetimeReservation', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/dashboard.html.twig', [
            'reservations' => $reservations,
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
}