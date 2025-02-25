<?php
namespace App\Controller;

use App\Repository\ReparationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReparationType;


#[Route('/reparation', name: 'reparation_')]
class ReparationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReparationRepository $reparationRepository): Response
    {
        $reparations = $reparationRepository->findAll();
        return $this->render('reparation/index.html.twig', [
            'reparations' => $reparations,
        ]);
    }
    public function create(Request $request, EntityManagerInterface $entityManager): Response
{
    $reparation = new Reparation();
    $form = $this->createForm(ReparationType::class, $reparation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reparation);
        $entityManager->flush();

        return $this->redirectToRoute('reparation/create.html.twig'); // Redirection après création
    }

    return $this->render('produit/create.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
