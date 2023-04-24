<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VodController extends AbstractController
{
    #[Route('/vod', name: 'app_vod')]
    public function index(): Response
    {
        return $this->render('vod/index.html.twig', [
            'controller_name' => 'VodController',
        ]);
    }
}
