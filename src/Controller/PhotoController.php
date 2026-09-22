<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PhotoRepository;

final class PhotoController extends AbstractController
{
    #[Route('/photos', name: 'app_photos')]
    public function index(PhotoRepository $repo): Response
    {
        $photos = $repo->findBy(['validee' => true]);

        return $this->render('photo/index.html.twig', [
            'photos' => $photos
        ]);
    }
}
