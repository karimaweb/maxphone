<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Entity\Image;
use App\Controller\Admin\ImageCrudController;
use App\Entity\Produit;
use App\Entity\Reparation;
use App\Entity\RendezVous;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Controller\Admin\UserCrudController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');// affichage sur la vue tableau de bord administrateur
    }

    public function configureDashboard(): \EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard
    {
        return \EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard::new()
            ->setTitle('MaxPhone Administration');
    }

    public function configureMenuItems(): iterable
    {
        return [
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Catégories', 'fas fa-list', Categorie::class),
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Utilsateurs', 'fas fa-user', Utilisateur::class),
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Images', 'fas fa-image', Image::class),
            
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Produits', 'fas fa-box', Produit::class),
            
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Réparations', 'fas fa-tools', Reparation::class),
           
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Rendez-vous', 'fas fa-calendar-check', RendezVous::class),
          
            \EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud('Tickets', 'fas fa-ticket-alt', Ticket::class),
        ];
}
#[Route('/admin/send-activation/{id}', name: 'admin_send_activation')]
public function sendActivation(Utilisateur $user, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
{
    // Générer un code à 6 chiffres
    $code = random_int(100000, 999999);

    // Enregistrer le code en base de données
    $activationCode = new ActivationCode();
    $activationCode->setUtilisateur($user);
    $activationCode->setCode((string) $code);

    $entityManager->persist($activationCode);
    $entityManager->flush();

    // Envoyer l'email au client
    $email = (new Email())
        ->from('noreply@votre-site.com')
        ->to($user->getEmail())
        ->subject('Votre code d’activation')
        ->text("Votre code d'activation est : " . $code);

    $mailer->send($email);

    $this->addFlash('success', 'Code d’activation envoyé à l’utilisateur.');
    return $this->redirectToRoute('admin_dashboard');
}
}