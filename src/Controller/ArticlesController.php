<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Form\ArticleAuthorType;
use App\Form\ArticleTaggedType;
use App\Form\SearchArticleByAuthorType;
use App\Form\SearchArticleByTagType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/articles")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles_index", methods="GET")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/list.html.twig', ['articles' => $articleRepository->findAll()]);
    }

    /**
     * @Route("/by-author", name="articles_by_author", methods="GET")
     */
    public function byAuthor(ArticleRepository $articleRepository, Request $request): Response
    {
        $searchForm = $this->createForm(SearchArticleByAuthorType::class, new Article());
        $searchForm->handleRequest($request);

        return $this->render('article/list_by_author.html.twig', [
            'articles' => $articleRepository->findByAuthor(),
            'search_form' => $searchForm->createView($searchForm->getData()->authorScreen ? $searchForm->getData()->authorScreen->getId() : null),
        ]);
    }

    /**
     * @Route("/tagged", name="articles_by_tag", methods="GET")
     */
    public function tagged(ArticleRepository $articleRepository, Request $request): Response
    {
        $searchForm = $this->createForm(SearchArticleByTagType::class, new Article());
        $searchForm->handleRequest($request);

        return $this->render('article/list_tagged.html.twig', [
            'articles' => $articleRepository->findByTag($searchForm->getData()->tagsScreen ? $searchForm->getData()->tagsScreen->getId() : ''),
            'search_form' => $searchForm->createView(),
        ]);
    }

    /**
     * @Route("/new-by-author", name="article_new_by_author", methods="GET|POST")
     */
    public function newByAuthor(Request $request): Response
    {
        $article = new Article();
        $article->setAvailableFrom(new \DateTime());

        $form = $this->createForm(ArticleAuthorType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // добавляем автора
            $article->addAuthor($article->authorScreen);

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('article/new_by_author.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new-tagged", name="article_new_tagged", methods="GET|POST")
     */
    public function newTagged(Request $request): Response
    {
        $article = new Article();
        $article->setCreatedAt(time());
        $article->setAvailableFrom(new \DateTime());

        $form = $this->createForm(ArticleTaggedType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // форма успешно проверена, зафиксируем ключевые слова
            $tags = explode(',', $article->tagsScreen);
            for ($i = 0; $i < 3; $i++) {
                if (isset($tags[$i])) {
                    $tag = trim($tags[$i], ', ');
                    $tag = trim($tag);
                    $model = $this->getDoctrine()
                        ->getRepository(Tag::class)
                        ->findOneBy(['title' => $tag]);
                    if (empty($model)) {
                        $model = new Tag();
                        $model->setTitle($tag);
                        $em->persist($model);
                        $em->flush();
                    }
                    $article->addTag($model);
                }
                else break;
            }

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_index');
        }

        return $this->render('article/new_by_author.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="article_show", methods="GET")
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/{slug}/edit", name="article_edit", methods="GET|POST")
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleAuthorType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_edit', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods="DELETE")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('articles_index');
    }
}
