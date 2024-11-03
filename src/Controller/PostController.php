<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']); // TODO: ALLE posts waarvan account != private
        $imagesData = [];
        $imagesMimeType = [];

        foreach ($posts as $post) {
            if ($post->getImage()) {
                $imagesData[] = base64_encode(stream_get_contents($post->getImage()));
                $imagesMimeType[] = mime_content_type($post->getImage());
            } else {
                $imagesData[] = null;
                $imagesMimeType[] = null;
            }
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'imagesData' => $imagesData,
            'imagesMimeType' => $imagesMimeType,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if user is verified
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', 'Je moet eerst je email verifiëren voordat je posts kunt maken.');
            return $this->redirectToRoute('app_post_index');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $post->setImage(file_get_contents($imageFile->getPathname()));
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner of the post
        if ($post->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Je kunt alleen je eigen posts bewerken.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $post->setImage(file_get_contents($imageFile->getPathname()));
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner of the post
        if ($post->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Je kunt alleen je eigen posts verwijderen.');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index');
    }

    #[Route('/{id}/image', name: 'app_post_image', methods: ['GET'])]
    public function getImage(Post $post): Response
    {
        if (!$post->getImage()) {
            throw $this->createNotFoundException('Geen afbeelding gevonden.');
        }

        $imageData = stream_get_contents($post->getImage());

        return new Response(
            $imageData,
            Response::HTTP_OK,
            ['Content-Type' => 'image/jpeg']
        );
    }
}
