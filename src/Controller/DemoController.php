<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DemoController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em, PostRepository $postRepository): Response
    {
        // Step 1:
        // $post = new Post;
        // $post->translate('fr')->setTitle('mon titre en français');
        // $post->translate('en')->setTitle('my title in english');
        // $post->translate('fr')->setContent('mon contenu en français');
        // $post->translate('en')->setContent('my content in english');
        // $em->persist($post);
        // $post->mergeNewTranslations();
        // $em->flush();

        // $post = $postRepository->find(1);
        // dd($post->translate('en')->getTitle(), $post->translate('fr')->getContent());

        // dd('done');

        $post = $postRepository->find(1);
        
        $form = $this->createFormBuilder($post)
            ->add('translations', TranslationsType::class, [
                'locales' => ['en', 'fr'],
                'default_locale' => ['en'],
                'required_locales' => ['fr'],
                'fields' => [
                    'title' => [
                        'field_type' => TextareaType::class,
                        'label' => 'toto',
                        'locale_options' => [
                            'en' => ['label' => 'mama'],
                            'fr' => ['label' => 'titre']
                        ]
                    ]
                ],
                'locale_labels' => [
                    'fr' => 'Français',
                    'en' => 'English',
                ],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_posts_show', ['id' => $post->getId()]);
        }

        return $this->renderForm('demo/index.html.twig', compact('form'));
    }

    #[Route('/posts/{id}', name: 'app_posts_show')]
    public function show(Post $post)
    {
        return $this->renderForm('demo/show.html.twig', compact('post'));
    }
}
