<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    // indique le nom de la route grace à l'annotation(@)
    /** 
     * @route("/", name="home") 
     */

     // render : fichier twig a afficher
    public function home() {

   //   $article= $this->getDoctrine()->getRepository(Article::class)->findSomeRecipe();

       return $this->render('blog/home.html.twig', [
           'title' => "Les Rois Du Gato",
         //  'article'=>$article
           ]);
    }

     /** 
     * @route("/recettes", name="recettes") 
     */
    public function allRecipe()
    {
       // recupère les champs de la table
       $repo = $this->getDoctrine()->getRepository(Article::class);

      $articles = $repo->findAll();

        return $this->render('blog/allRecipe.html.twig', [
         'title' => "Toutes les recettes",
         'articles' => $articles
        ]);

        }
          /** 
     * @route("/recette/{id}", name="recette") 
     */
   public function showRecipe($id){

   $repo = $this->getDoctrine()->getRepository(Article::class);
   
   $articles = $repo->find($id);

     return $this->render('blog/showRecipe.html.twig', [
      'articles' => $articles
     ]);
 }

          /** 
     * @route("/new", name="new") 
     */
 function create(Request $request, ObjectManager $manager)
 {

   $article = new Article();

   $form = $this->createFormBuilder($article)
                        ->add('titre')
                        ->add('difficulte')
                        ->add('createdAt')
                        ->add('modifiedAt')
                        ->add('image')
                        ->add('ingredients')
                        ->add('preparation')
                        ->add('cuisson')
                        ->add('recette')
                        ->getForm();

    $form->handleRequest($request);

   if($form->isSubmitted() && $form->isValid()) {


         $manager->persist($article);
         $manager->flush();

   }
 
     return $this->render('blog/create.html.twig', [
      'title' => "Nouvelle Recette" , 'form' => $form->createView()
     ]);
 }


}
