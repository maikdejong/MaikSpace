<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index()
    {
        $user = $this->getUser() ? $this->getUser()->getEmail() : null;

        return $this->render('homepage/index.html.twig');
    }
}