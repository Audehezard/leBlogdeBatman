<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * Page d'inscription
     *
     * @Route("/creer-un-compte/", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, RecaptchaValidator  $recaptcha): Response
    {

        // Si l'utilisateur est déjà connecté on le redirige sur l'accueil
        if($this->getUser()){
            return $this->redirectToRoute('main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // $_POST['g-recaptcha-response']
            $captchaResponse = $request->request->get('g-recaptcha-response', null);

            // $_SERVER['REMOTE_ADDR']
            $ip = $request->server->get('REMOTE_ADDR');

            // Vérification de la validité du captcha (sinon erreur)
            if($captchaResponse == null || !$recaptcha->verify( $captchaResponse, $ip )){
                $form->addError( new FormError('Veuillez remplir le captcha de sécurité') );
            }

            // Si le formulaire n'a pas d'erreur
            if($form->isValid()){

                // encode the plain password
                $user
                    ->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )

                //Hydratation de la base d'inscription de l'utilisateur
            ->setRegistrationDate( new dateTime() ))
            ;


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //on créer un message flash de succès
            $this->addFlash('success','votre compte a été créé avec succès !);');

            return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
