<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Form\UserSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserSettingsController extends AbstractController
{
    #[Route('/user-settings', name: 'app_user_settings')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Check if user is verified
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', 'You must verify your email first.');
            return $this->redirectToRoute('app_post_index');
        }

        $user = $this->getUser();
        $userSettings = $this->getUser()->getUserSettings();

        $imagesData = [];

        if ($userSettings->getProfilePicture()) {
            $imagesData = [
                'profile_picture' => $userSettings->getProfilePicture(),
            ];
        } else {
            $imagesData = [
                'profile_picture' => null,
            ];
        }

        $form = $this->createForm(UserSettingsType::class, $userSettings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();
            $bio = $form->get('bio')->getData();

            if (!$userSettings) {
                $userSettings = new UserSettings();
                /** @var User $user */
                $userSettings->setUser($user);
            }

            $userSettings->setBio($bio);
            $userSettings->setUsername($username);
            $imageFile = $form->get('profile_picture')->getData();
            if ($imageFile) {
                $userSettings->setProfilePicture(file_get_contents($imageFile->getPathname()));
            }

            $entityManager->persist($userSettings);
            $entityManager->flush();

            $this->addFlash('success', 'User settings saved successfully!');

            return $this->redirectToRoute('app_user_settings');
        }

        return $this->render('user_settings/index.html.twig', [
            'form' => $form->createView(),
            'imagesData' => $imagesData,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/image', name: 'app_user_settings_image', methods: ['GET'])]
    public function getImage(UserSettings $userSettings): Response
    {
        if (!$userSettings->getProfilePicture()) {
            throw $this->createNotFoundException('Geen afbeelding gevonden.');
        }

        $imageData = stream_get_contents($userSettings->getProfilePicture());

        return new Response(
            $imageData,
            Response::HTTP_OK,
            ['Content-Type' => 'image/jpeg']
        );
    }
}
