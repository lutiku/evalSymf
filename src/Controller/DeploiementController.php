<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DeploiementController extends AbstractController
{
    /**
     * @Route("/", name="deploiement")
     */
    public function index()
    {
        return $this->render('deploiement/index.html.twig', [
            'controller_name' => 'DeploiementController',
        ]);
    }
}
