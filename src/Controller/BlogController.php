<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Préfixes de la route et du nom des pages du blog
 *
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * Page admin permettant de créer une nouvelle publication
     *
     * @Route("/nouvelle-publication/", name="new_publication")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function newPublication(Request $request): Response
    {

        $newArticle = new Article();

        $form = $this->createForm(NewArticleFormType::class, $newArticle);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $newArticle
                ->setAuthor($this->getUser())
                ->setPublicationDate( new DateTime() )
            ;

            $em = $this->getDoctrine()->getManager();

            $em->persist($newArticle);

            $em->flush();

            $this->addFlash('success', 'Article publié avec succès !');

            // TODO: Changer la redirection vers la page de l'article créé
            return $this->redirectToRoute('main_home');

        }

        return $this->render('blog/newPublication.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
        * Page admin permettant d'afficher les publications
         *
        * @Route("/publication/liste", name="publication_list")

        */
       public function PublicationList (): Response
       {
           $articleRepo = $this->getDoctrine()->getRepository(Article::class);
           $articles = $articleRepo->findAll();
           return $this->render('blog/PublicationList.html.twig', [
               'articles' => $articles,
           ]);
       }
}
