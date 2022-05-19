<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Recaptcha\RecaptchaValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    /**
     * Contrôleur de la page d'inscription
     */
    #[Route('/creer-un-compte/', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, RecaptchaValidator $recaptcha): Response
    {
        // Si utilisateur déjà connecté, redirection vers l'accueil
        if($this->getUser()){
            return $this->redirectToRoute('main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // Récupération de $_POST['g-recaptcha-response'], sinon null
            $captchaResponse = $request->request->get('g-recaptcha-response', null);

            // Récupération de l'adresse IP du client
            $ip = $request->server->get('REMOTE_ADDR');

            // Si le formulaire n'est pas valide, on ajoute une erreur au formulaire
            if(!$recaptcha->verify($captchaResponse, $ip)){

                $form->addError(new FormError('Veuillez remplir le captcha de sécurité') );
            }

            if ($form->isValid()) {
                // Hydratation du mot de passe avec le hashage du mot de passe venant du formulaire
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Hydratation de la date d'inscription
                $user->setRegistrationDate(new \DateTime());

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                // Message flash de succès
                $this->addFlash('success', 'Votre compte a été créeé avec succès ! ');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
