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

    #[Route('/admin/users', name: 'admin_users')]
    public function users(UtilisateurRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
}