<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    #[Route("/register", name: "app_register")]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($form->get('plainPassword')->getData());

            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route("/verify", name: "app_verify_email")]
    public function verifyUserEmail()
    {
        //todo
    }

    #[Route("/login", name: "app_login")]
    public function login()
    {
        if ($this->getUser()) {
            return $this->render('homepage/index.html.twig');
        }

        return $this->render('security/login.html.twig');
    }

    #[Route("/logout", name: "app_logout")]
    public function logout()
    {
        // symfony handles this
    }
}
