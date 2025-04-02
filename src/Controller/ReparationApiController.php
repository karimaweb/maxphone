<?php

namespace App\Controller;

use App\Repository\ReparationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ReparationApiController extends AbstractController
{
    #[Route('/mes-reparations', name: 'api_mes_reparations', methods: ['GET'])]
    public function getMesReparations(ReparationRepository $repo): JsonResponse
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour voir vos réparations.'], 403);
        }

        // Récupère les réparations associées à l'utilisateur connecté
        $reparations = $repo->findBy(['utilisateur' => $user]);

        if (!$reparations || count($reparations) === 0) {
            return new JsonResponse(['message' => ' Aucune réparation trouvée.']);
        }

        // Structure les données pour le frontend
        $data = [];

        foreach ($reparations as $rep) {
            $data[] = [
                'produit' => $rep->getProduit()->getLibelleProduit(),
                'dateDepot' => $rep->getDateHeureReparation()->format('d/m/Y'),
                'statut' => $rep->getStatutReparation(),
            ];
        }

        return new JsonResponse($data);
    }
}
