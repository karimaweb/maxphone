<?php

namespace App\Controller;
use App\Entity\Produit; // Ajoute l'import de l'entité Produit
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit', name: 'produit_')]
final class ProduitController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $produits = $produitRepository->findAll();
        $categories = $categorieRepository->findParentCategories(); // Récupérer uniquement les catégories parent

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories // Passer les catégories parent au template
        ]);
    }
    #[Route('/{id}', name: 'detail')]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('produit/detail.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/create', name: 'produit_create')]
    public function createProduit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

        //     return $this->redirectToRoute('produit_detail', ['id' => $produit->getId()]);
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
