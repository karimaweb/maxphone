<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Repository\HistoriqueReparationRepository;

class HistoriqueReparationController extends AbstractController
{
    #[Route('/api/mes-reparations', name: 'api_mes_reparations', methods: ['GET'])]
public function getMesReparations(HistoriqueReparationRepository $repository, Security $security): JsonResponse
{
    $user = $security->getUser();

    if (!$user) {
        return new JsonResponse(['error' => ' Utilisateur non connecté'], 401);
    }

    // je récupérer le statut de  réparation qui appartiennent à l'utilisateur connecté via `Reparation`
    $reparations = $repository->createQueryBuilder('h')
    ->join('h.reparation', 'r')
    ->join('r.produit', 'p')
    ->where('r.utilisateur = :user')
    ->setParameter('user', $user)
    ->orderBy('h.dateMajReparation', 'DESC')
    ->setMaxResults(1) //  Garde uniquement le dernier statut
    ->getQuery()
    ->getResult(); //  Récupère des objets Doctrine (et non un array)



    if (!$reparations) {
        return new JsonResponse(['message' => ' Aucune réparation trouvée.']);
    }

    $data = [];
foreach ($reparations as $reparation) {
    $data[] = [
        'id' => $reparation->getId(),
        'produit' => $reparation->getReparation()->getProduit()->getLibelleProduit(),
        'dateDepot' => $reparation->getDateMajReparation()->format('Y-m-d H:i'),
        'statut' => $reparation->getStatutHistoriqueReparation(), //  Dernier statut uniquement
    ];
}

return new JsonResponse($data);

}
}