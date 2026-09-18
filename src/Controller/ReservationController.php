<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'reservation')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $reservation->setDateCreation(new \DateTime());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reservation->setUtilisateur($this->getUser());

            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Réservation enregistrée !');

            return $this->redirectToRoute('reservation');
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/delete/{id}', name: 'reservation_delete')]
    public function deleteReservation(Reservation $reservation, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($reservation->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($reservation);
        $em->flush();

        return $this->redirectToRoute('app_account');
    }
}