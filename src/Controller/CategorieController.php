<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;


class CategorieController extends AbstractController
{
    
    public function index(CategorieRepository $categorieRepo): Response
    {
        // Récupère toutes les catégories
        $categorie = $categorieRepo->findAll();

        // Rendu d'un template "categorie/index.html.twig"
        return $this->render('categorie/accessoire.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/accessoire', name: 'app_categorie_accessoire')]
    public function accessoires(ProduitRepository $produitRepo): Response
    {
        // On filtre les produits de la catégorie "Accessoires"
        $produits = $produitRepo->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->where('c.nomCategorie = :cat')
            ->setParameter('cat', 'Accessoires')
            ->getQuery()
            ->getResult();

        return $this->render('categorie/accessoire.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/categorie/{id}', name: 'app_categorie_show')]
    public function show(int $id, CategorieRepository $repo): Response

    {
        $categorie = $repo->find($id);

        return $this->render('categorie/show.html.twig', [
        'categorie' => $categorie, // On passe bien la clé 'categorie'
    ]);
}
}
