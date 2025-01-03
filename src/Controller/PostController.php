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
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $userRoles)) {
            $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);
        } else {
            $posts = $postRepository->findBy(
                ['user' => $user],
                ['createdAt' => 'DESC']
            );
        }

        $imagesData = [];

        foreach ($posts as $post) {
            if ($post->getImage()) {
                $imagesData[] = base64_encode(stream_get_contents($post->getImage()));
            } else {
                $imagesData[] = null;
            }
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'imagesData' => $imagesData,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if user is verified
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', 'You must verify your email first.');
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
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        // Check if user is admin or owner of the post
        if (!in_array('ROLE_ADMIN', $userRoles) && $post->getUser() !== $user) {
            $this->addFlash('error', 'Je kunt alleen je eigen posts bewerken.');
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
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        /// Check if user is admin or owner of the post
        if (!in_array('ROLE_ADMIN', $userRoles) && $post->getUser() !== $user) {
            $this->addFlash('error', 'Je kunt alleen je eigen posts verwijderen.');
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
