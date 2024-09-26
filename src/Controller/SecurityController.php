<?php

namespace App\Controller;


use AllowDynamicProperties;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route("/register", name: "app_register")]
    public function register(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($form->get('plainPassword')->getData());

            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            $this->em->persist($user);
            $this->em->flush();

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->addFlash('success', 'Confirm your email at: '.$signatureComponents->getSignedUrl());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route("/verify", name: "app_verify_email")]
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        $user = $this->userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }
        $user->setVerified(true);
        $this->em->flush();

        $this->addFlash('success', 'Account Verified! You can now log in.');
        return $this->redirectToRoute('app_login');
    }

    #[Route("/login", name: "app_login")]
    public function login()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('security/login.html.twig');
    }

    #[Route("/logout", name: "app_logout")]
    public function logout()
    {
        // symfony handles this
    }
}
