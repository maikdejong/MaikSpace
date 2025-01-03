<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $em,
        private readonly EmailVerifier $emailVerifier,
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            $this->em->persist($user);
            $this->em->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('maik-de-jong@live.nl', 'MaikMail Registration'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_homepage');
    }

    #[Route("/login", name: "app_login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route("/logout", name: "app_logout")]
    public function logout()
    {
        // symfony handles this
    }

    #[Route("/enable-2fa", name: "app_2fa_enable")]
    #[isGranted('ROLE_USER')]
    public function enable2fa(TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        $user = $this->getUser();

        if (!$user->isTotpAuthenticationEnabled()) {
            $user->setTotpSecret($totpAuthenticator->generateSecret());
            $this->em->flush();
        }

        return $this->render("security/enable_2fa.html.twig");
    }

    #[Route("/confirm_2fa", name: "app_confirm_2fa")]
    #[isGranted('ROLE_USER')]
    public function confirm2fa(TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        $user = $this->getUser();

        $user->setIsTotpEnabled(true);
        $this->em->flush();

        return $this->redirectToRoute('app_homepage');
    }

    #[Route("/disable-2fa", name: "app_2fa_disable")]
    #[isGranted('ROLE_USER')]
    public function disable2fa(TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        $user = $this->getUser();

        if ($user->isTotpAuthenticationEnabled()) {
            $user->setTotpSecret(null);
            $user->setIsTotpEnabled(false);
            $this->em->flush();
        }

        return $this->render("security/disable_2fa.html.twig");
    }

    #[Route("/authentication/2fa/qrcode", name: "app_2fa_qrcode")]
    #[isGranted('ROLE_USER')]
    public function displayGoogleAuthenticatorQrCode(TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        /** @var TwoFactorInterface $user */
        $user = $this->getUser();

        $qrCodeContent = $totpAuthenticator->getQRContent($user);
        $result = Builder::create()
            ->data($qrCodeContent)
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
