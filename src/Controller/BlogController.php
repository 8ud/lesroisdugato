<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use App\Entity\Category;
use App\Entity\Article;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;


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

      // $user =$this-> getUser()->getUsername();
      // dump($user);
      $articles= $this->getDoctrine()->getRepository(Article::class)->findSomeRecipe();

       return $this->render('blog/home.html.twig', [
           'title' => "Les Rois Du Gato",
           'articles'=>$articles,
         //   'user'=>$user
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
   public function showRecipe(Article $articles, Request $request, $id, ObjectManager $manager){

   $repo = $this->getDoctrine()->getRepository(Article::class);

    $articles = $repo->find($id);

   // création du formulaire

  // récupération de l'utilisateur connecté
  if(($user =$this->getUser()) !== null){

     $user =$this->getUser()->getUsername();
  }else {
     $user= '';
  }
dump($user);
      // on crée  un commentaire
      $comment = new Comment();

      // on rajoute les informations automatiquement
      $comment->setCreatedAt(new \DateTime());
      $comment->setArticle($articles);
      $comment->setAuthor($user);

      //on recupère le formulaire
      $form = $this->createForm(CommentType::class, $comment);

       $form->handleRequest($request);
       //si le formulaire à été soumis

       if($form->isSubmitted() && $form->isValid()) {

          //on enregistre le produit dans la base de donnée
          $manager->persist($comment);
          $manager->flush();

          return $this->redirectToRoute('recette', [ 'id' => $articles->getId()]);

         }



     return $this->render('blog/showRecipe.html.twig', [
      'articles' => $articles,
      'commentForm' => $form->createView()


     ]);
}

          /**
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


   }
   // on rend la vue
     return $this->render('blog/create.html.twig', [
      'title' => "Nouvelle Recette" ,
       'form' => $form->createView()
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
//dump($article);

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
                'article'   => $article,
                'id' => $article->getId()
      ]);
  }



}
