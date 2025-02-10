<?php

namespace App\Controller;
use App\Repository\ProduitRepository; // Assurez-vous que cette ligne est présente

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ProduitRepository $produitRepository): Response
{
    $produits = $produitRepository->findAll();

    return $this->render('main/index.html.twig', [
        'produits' => $produits,
    ]);
}
}
