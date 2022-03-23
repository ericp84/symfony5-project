<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account", methods="GET")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    /**
     * @Route("/account/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vote compte à bien été mis à jour ! 🚀');

            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/change-password", name="app_account_change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher) : Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_required' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form['plainPassword']->getData())
            );
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe à bien été modifié ! 🔑');

            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
