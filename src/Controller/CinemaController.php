<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CinemaController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('Cinema/index.html.twig');
    }
}
