<?php

namespace App\Controller;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * Contrôleur de la page d'accueil
     *
     * @Route("/", name="main_home")
     */
    public function home(): Response
    {

        // Récupération des derniers articles publiés
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleRepo->findBy([], ['publicationDate' => 'DESC'], $this->getParameter('app.article.last_article_number'));

        return $this->render('main/home.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * Contrôleur de la page profil
     *
     * @Route("/mon-profil/", name="main_profile")
     * @Security("is_granted('ROLE_USER')")
     */
    public function profile(): Response
    {
        return $this->render('main/profile.html.twig');
    }
    /**
     * @Route("/edit-photo/", name="main_edit_photo")
     * @security("is_granted('ROLE_USER')")
     */
    public function editPhoto():Response
    {
        return $this->render('main/editPhoto.html.twig');
    }

}
