<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
