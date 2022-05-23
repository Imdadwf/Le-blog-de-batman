<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\EditPhotoFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('', name:'main_')]
class MainController extends AbstractController
{

    /**
     * Contrôleur de la page d'accueil
     */
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {

        // Récupération des derniers articles à afficher sur l'accueil
        $articleRepos = $doctrine->getRepository(Article::class);

        $articles = $articleRepos->findBy(
            [], // WHERE du SELECT
            ['publicationDate' => 'DESC'],  // ORDER BY du SELECT
            $this->getParameter('app.article.last_article_number_on_home')  // LIMIT du SELECT (qu'on récupère dans service.yaml)
        );


        return $this->render('main/home.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * Contrôleur de la page de profils
     * Accès réservé au connectés (ROLE_USER)
     */

    #[Route('/mon-profil/', name: 'profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }

    /**
     * Contrôleur de la page de modification de la photo de profil
     *
     * Accès réserver aux connectés (ROLE_USER)
     */
    #[Route('/editer-photo/', name: 'edit_photo')]
    #[IsGranted('ROLE_USER')]
    public function editPhoto(Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(EditPhotoFormType::class);

        $form->handleRequest($request);

        // Si le formulaire a été envoyé et n'a pas d'erreur
        if($form->isSubmitted() && $form->isValid()){

            // Récupération des infos de la photo envoyée
            $photo = $form->get('photo')->getData();

            // Si l'utilisateur à déjà une photo de profil, on la supprime
            if(
                $this->getUser()->getPhoto() != null &&
                file_exists($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() )
                ){

                unlink($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() );
            }


            // Création d'un nouveau nom pour la photo (tant que le nom est déjà pris on en regénère un)
            do{

                $newFileName = md5(random_bytes(100)) . '.' . $photo->guessExtension();

            }while(file_exists( $this->getParameter('app.user.photo.directory') . $newFileName ) );

            // Sauvegarde du nom de la photo dans l'utilisateur connecté
            $this->getUser()->setPhoto($newFileName);

            // Sauvegarde en BDD
            $em = $doctrine->getManager();
            $em->flush();

            // Déplacement physique de l'image dans le dossier paramétré dans service.yaml
            $photo->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName,
            );

            // Message flash de succès
            $this->addFlash('success', 'Photo de profil modifiée avec succès !');

            // Redirection vers le profil
            $this->redirectToRoute('main_profil');
        }

        return $this->render('main/edit_photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
