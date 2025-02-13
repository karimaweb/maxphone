<?php

namespace App\Controller;
use App\Repository\ProduitRepository; // Assurez-vous que cette ligne est présente

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

Class MainController extends AbstractController
{
    #[Route('/', name: 'main_index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findProduitsEnVente(); // Récupère seulement les produits en vente
        return $this->render('main/index.html.twig', [
            'produits' => $produits,
        ]);
    }
}
