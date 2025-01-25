<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class JornalController extends AbstractController
{
    #[Route('/journal', name: 'journal')]
    public function list(): Response
    {

        return $this->render('journal/journal.html.twig');
    }
}