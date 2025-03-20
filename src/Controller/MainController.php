<?php

namespace App\Controller;
use App\Repository\ProduitRepository; // Assurez-vous que cette ligne est présente

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\CategorieRepository;

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
    #[Route('/recherche', name: 'app_recherche')]

public function search(
    Request $request,
    ProduitRepository $produitRepo,
    CategorieRepository $categorieRepo
): Response {
    $query = $request->query->get('q', '');
    $produits = [];
    $categories = [];

    if ($query) {
        $produits = $produitRepo->findBySearchTerm($query);
        $categories = $categorieRepo->findBySearchTerm($query);
    }

    return $this->render('recherche/resultats.html.twig', [
        'query' => $query,
        'produits' => $produits,
        'categories' => $categories,
    ]);
}

}
