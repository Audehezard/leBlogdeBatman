<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        if ($form->isSubmitted() && $form->isValid()) {

            $newArticle
                ->setAuthor($this->getUser())
                ->setPublicationDate(new DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($newArticle);

            $em->flush();

            $this->addFlash('success', 'Article publié avec succès !');


            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $newArticle->getSlug(),
            ]);

        }

        return $this->render('blog/newPublication.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Page qui liste tous les articles
     *
     * @Route("/publications/liste/", name="publication_list")
     */
    public function publicationList(Request $request, PaginatorInterface $paginator): Response
    {

        // Récupération du numéro de la page demandée dans l'URL
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le numéro est positif
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');

        // Récupération des articles
        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );


        return $this->render('blog/publicationList.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Page permettant de voir un article en détail
     *
     * @Route("/publication/{slug}/", name="publication_view")
     */
    public function publicationView(Article $article): Response
    {

        return $this->render('blog/publicationView.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/recherche/", name="search")
     */
    public function search(Request $request, PaginatorInterface $paginator): Response
    {

        // Récupération du numéro de la page demandée dans l'URL
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le numéro est positif
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q', '');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.content LIKE :search ORDER BY a.publicationDate DESC')
            ->setParameters([
                'search' => '%' . $search . '%',
            ]);

        // Récupération des articles
        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );


        return $this->render('blog/listSearch.html.twig', [
            'articles' => $articles,
        ]);

    }

    /**
     * Page admin servant à supprimer un article via son id passé dans l'URL
     *
     * @Route("/publication/suppression/{id}/", name="publication_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function publicationDelete(Article $article, Request $request): Response
    {

        if(!$this->isCsrfTokenValid('blog_publication_delete_' . $article->getId(), $request->query->get('csrf_token'))){

            $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');
        } else {

            // Manager général
            $em = $this->getDoctrine()->getManager();

            // Suppression de l'article
            $em->remove($article);
            $em->flush();

            // Message flash de succès + redirection sur la liste des articles
            $this->addFlash('success', 'La publication a été supprimée avec succès !');
        }


        return $this->redirectToRoute('blog_publication_list');

    }


    /**
     * Page permettant aux admins de modifier un article
     *
     * @Route("/publication/modifier/{id}/", name="publication_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function publicationEdit(Article $article, Request $request): Response
    {

        $form = $this->createForm(NewArticleFormType::class, $article);

        $form->handleRequest($request);

        // Si formulaire ok
        if ($form->isSubmitted() && $form->isValid()) {

            // Mise à jour dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Publication modifiée avec succès !');

            return $this->redirectToRoute('blog_publication_view', [
                'slug' => $article->getSlug(),
            ]);

        }

        return $this->render('blog/publicationEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}


