<?php
//
//namespace App\Controller;
//
//use App\Entity\UserSettings;
//use App\Form\UserSettingsType;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Attribute\Route;
//
//class UserSettingsController extends AbstractController
//{
//    #[Route('/user-settings', name: 'app_user_settings')]
//    public function index(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $userSettings = $this->getUser()->getUserSettings();
//
//        $form = $this->createForm(UserSettingsType::class, $userSettings);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $formData = $form->getData();
////            dd($formData);
//            $userSettingsRepository = $entityManager->getRepository(UserSettings::class);
//
//            $userSettingsExist = $userSettingsRepository->findBy([
//                'user' => $this->getUser(),
//            ]);
//
//            if ($userSettingsExist) {
//                $userSettingsExist->setBio($formData->getBio());
//            }
//
//
//
//            $entityManager->persist($userSettingsExist);
//            $entityManager->flush();
//            $this->addFlash('success', 'User settings updated successfully.');
//            return $this->redirectToRoute('app_homepage');
//        }
//
//        return $this->render('user_settings/index.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
//}
