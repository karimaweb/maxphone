<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

use App\Entity\ActivationCode;

class AppAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager,UrlGeneratorInterface $urlGenerator ) 
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $password = $request->getPayload()->getString('password');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    //  Vérification du code d'activation
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

         if ($user) {
        $activationCode = $this->entityManager
            ->getRepository(ActivationCode::class)
            ->findOneBy(['utilisateur' => $user]);

        if ($activationCode) {
            throw new CustomUserMessageAuthenticationException(
                "Votre compte n'a pas encore été activé. <a href='/activation' class='alert-link'>Cliquez ici pour l’activer</a>."
            );
            
        }
    }

    return new Passport(
        new UserBadge($email),
        new PasswordCredentials($password)
    );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
    
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('admin')); //  Redirige vers `/admin`
        }
    
        return new RedirectResponse($this->urlGenerator->generate('main_index')); // Redirige `ROLE_USER` vers `/`
    
    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
