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
    public function activateAccount(Request $request, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Afficher le formulaire en mode GET
        if ($request->isMethod('GET')) {
            return $this->render('activation/activation.html.twig');

        }

        // Récupérer les données envoyées par le formulaire
        $submittedToken = $request->request->get('_csrf_token');
        $email = $request->request->get('email');
        $code = $request->request->get('code');

        // Vérifier le token CSRF
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('activate_account', $submittedToken))) {
            $this->addFlash('error', '❌ Erreur de sécurité : token CSRF invalide.');
            return $this->redirectToRoute('app_activation');
        }

        // Vérifier si l'utilisateur existe
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', '❌ Utilisateur introuvable.');
            return $this->redirectToRoute('app_activation');
        }

        // Vérifier si l'utilisateur est déjà activé
        if (in_array('ROLE_USER', $user->getRoles())) {
            $this->addFlash('error', 'Votre compte est déjà activé.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier le code d'activation
        $activationCode = $entityManager->getRepository(ActivationCode::class)
            ->findOneBy(['utilisateur' => $user, 'code' => $code]);

        if (!$activationCode) {
            $this->addFlash('error', ' Code d’activation incorrect ou expiré.');
            return $this->redirectToRoute('app_activation');
        }

        // Activer le compte
        $user->setRoles(['ROLE_USER']);
        $entityManager->remove($activationCode); // Supprimer le code d'activation
        $entityManager->flush();

        // Message de succès et redirection vers la page de connexion
        $this->addFlash('success', 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
