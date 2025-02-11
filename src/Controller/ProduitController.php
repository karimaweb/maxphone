<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/produit', name: 'app_produit')]
final class ProduitController extends AbstractController
{
    
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            
        ]);
    }
}
