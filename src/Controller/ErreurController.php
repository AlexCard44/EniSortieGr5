<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErreurController extends AbstractController
{
    #[Route('/notFoundRedirect', name: 'notFoundRedirect')]
    public function handleNotFound(): Response
    {
        return $this->redirectToRoute('');
    }
}
