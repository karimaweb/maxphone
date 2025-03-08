<?php
namespace App\Controller;
use App\Entity\Utilisateur;
use App\Entity\ActivationCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ActivationController extends AbstractController
{
    #[Route('/activation', name: 'app_activation', methods: ['GET', 'POST'])]
    public function activateAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('activation/activation.html.twig');
        }

        // Récupérer les données envoyées par le formulaire le code d'activation et l'email
        $email = $request->request->get('email');
        $code = $request->request->get('code');

        // Vérifier si l'utilisateur existe 
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_activation');
        }

        // Vérifier si l'utilisateur est déjà activé
        if (in_array('ROLE_USER', $user->getRoles())) {
            $this->addFlash('error', 'Votre compte est  activé.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier le code d'activation
        $activationCode = $entityManager->getRepository(ActivationCode::class)
            ->findOneBy(['utilisateur' => $user, 'code' => $code]);

        if (!$activationCode) {
            $this->addFlash('error', 'Code d’activation incorrect ou expiré.');
            return $this->redirectToRoute('app_activation');
        }

        // Activer le compte
        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user); 
        $entityManager->remove($activationCode); // Supprimer le code d'activation après usage
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }

}
