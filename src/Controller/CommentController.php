<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\Common\Persistence\ObjectManager;



use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class CommentController extends AbstractController
{

  /**
       * @route("/comment/{article}", name="comment") 
     */
    public function createComment(Request $request, ObjectManager $manager, $article){

         // on recupère l'id de l'article
         $repo = $this->getDoctrine()->getRepository(Comment::class);
   
          $article = $repo->find($article);
   
      // on crée  un commentaire
      $comment = new Comment();
      //on recupère le formulaire
      $form = $this->createFormBuilder($comment)
                           ->add('author')
                           ->add('content', TextareaType::class)
                           ->add($article)
                           ->getForm();
   
       $form->handleRequest($request);
       // si le formulaire à été soumis
       if($form->isSubmitted() && $form->isValid()) {
          
          $comment->setCreatedAt(new \DateTime());
          $comment->setArticle($article);
          
      // on enregistre le produit dans la base de donnée
            $manager->persist($comment);
            $manager->flush();
   
    
   
      }
      // on rend la vue
        return $this->render('blog/comment.html.twig', [
         'title' => "Nouveau commentaire" ,
          'form' => $form->createView(),
          'article' => $article
        ]);
    }
}