<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

      $articles= $this->getDoctrine()->getRepository(Article::class)->findSomeRecipe();

       return $this->render('blog/home.html.twig', [
           'title' => "Les Rois Du Gato",
           'articles'=>$articles
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
     *  @return Response
     *
     * @route("/new", name="new") 
     */
   public function create(Request $request, ObjectManager $manager){

   
   // on crée  un article
   $article = new Article();
   //on recupère le formulaire
   $form = $this->createFormBuilder($article)
                        ->add('titre')
                        ->add('difficulte')
                        ->add('image')
                        ->add('category', EntityType::class, [
                              'class'=> Category::class,
                              'choice_label' => 'title'
                        ])
                        ->add('ingredients')
                        ->add('preparation')
                        ->add('cuisson')
                        ->add('recette')
                        ->getForm();

    $form->handleRequest($request);
    // si le formulaire à été soumis
    if($form->isSubmitted() && $form->isValid()) {
       
       $article->setCreatedAt(new \DateTime());
       
   // on enregistre le produit dans la base de donnée
         $manager->persist($article);
         $manager->flush();

         return new Response('Article ajouté !');

   }
   // on rend la vue
     return $this->render('blog/create.html.twig', [
      'title' => "Nouvelle Recette" , 'form' => $form->createView()
     ]);
 }

 /** 
     * @route("/edit", name="edit") 
     */
    public function editRecipe()
    {
       // recupère les champs de la table
       $repo = $this->getDoctrine()->getRepository(Article::class);

      $articles = $repo->findAll();

        return $this->render('blog/editRecipe.html.twig', [
         'title' => "Selectionez l'article à éditer",
         'articles' => $articles
        ]);

        }

   /**
     * @route("/update/{id}", name="update")
     */
    function update(Request $request, ObjectManager $manager,$id)
    {
      // on crée  un article
      $repo = $this->getDoctrine()->getRepository(Article::class);
   
   $article = $repo->find($id);
    
      //on recupère le formulaire
      $form = $this->createFormBuilder($article)
                            ->add('titre')
                            ->add('difficulte')
                            ->add('image')
                            ->add('ingredients')
                            ->add('preparation')
                            ->add('cuisson')
                            ->add('recette')
                           ->getForm();
   
       $form->handleRequest($request);
   
       // si le formulaire à été soumis
      if($form->isSubmitted() && $form->isValid()) {
         $article->setModifiedAt(new \DateTime());

      // on enregistre le produit dans la base de donnée
            $manager->persist($article);
            $manager->flush();

         //   return new Response('Article modifié !');
   
         return $this->redirectToRoute('edit');
   
      }
      // on rend la vue
        return $this->render('blog/updateRecipe.html.twig', [
         'title' => "Modification Recette" , 'form' => $form->createView()
        ]);
    }

     /**
     * @route("/delete/{id}", name="delete")
     */
    public function deleteRecipe($id) {
      $Manager = $this->getDoctrine()->getManager();
      $article = $Manager->getRepository(Article::class)->find($id);

      if (!$article) {
          throw $this->createNotFoundException(
                  'No product found for id ' . $id
          );
      }
dump($article);

      $Manager->remove($article);
      $Manager->flush();

      return $this->redirectToRoute('edit', [
                  'id' => $article->getId(),
                  'article'   => $article
      ]);
  }

  /**
     * @Route("/confirmDeleteRecipe/{id}", name="confirm")
     */
    public function confirmDelete($id) {
      $Manager = $this->getDoctrine()->getManager();
      $article = $Manager->getRepository(Article::class)->find($id);

      if (!$article) {
          throw $this->createNotFoundException(
                  'No product found for id ' . $id
          );
      }

      return $this->render('blog/delRecipe.html.twig', [
                'article'   => $article
      ]);
  }



}
