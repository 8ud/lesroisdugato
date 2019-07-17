<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager,  UserPasswordEncoderInterface $encoder) {

      $user = new user();

      $form = $this->createForm(RegistrationType::class, $user);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('');
      }


      return $this->render('security/registration.html.twig', [
           
            'form' => $form->createView(),
            'title' => "Inscription",
        ]);
    }

    /**
    * @route("/connexion", name="security_login")
    */
    public function login(){
       return $this->render('security/login.html.twig',[
          'title' => "Connection"
       ]);
    }

   
}
