<?php
namespace App\Controller;

use App\Repository\ReparationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
