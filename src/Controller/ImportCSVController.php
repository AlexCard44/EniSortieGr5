<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportCSVController extends AbstractController
{
    #[Route('/import/c/s/v', name: 'app_import_c_s_v')]
    public function index(): Response
    {
        return $this->render('import_csv/index.html.twig', [
            'controller_name' => 'ImportCSVController',
        ]);
    }
}
