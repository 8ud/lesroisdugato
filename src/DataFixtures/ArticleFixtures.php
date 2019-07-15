<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Faker;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
            $faker = Faker\Factory::create('fr_FR');

            // Créer 4 catégories fakée

            for($i = 1; $i <= 4; $i++){
               $category = new Category();
               $category ->setTitle($faker->word());

               $manager->persist($category);

               // Créer entre 4 et 6 articles
               for($j = 1; $j <= mt_rand(4, 6); $j++){
                  $article = new Article();

                  $content = '<p>' . join($faker->paragraphs(3), '<p></p>') . '<p>';
                  $article->setTitre($faker->word)
                  ->setDifficulte("facile")
                  ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                  ->setModifiedAt(new \DateTime())
                  ->setImage($faker->imageUrl(400, 300, 'food'))
                  ->setIngredients("<p>3oeuf, 200g de farine, 100g de sucre</p>")
                  ->setPreparation("20 min")
                  ->setCuisson("30 min")
                  ->setRecette($content)
                  ->setCategory($category);
                  
                  $manager->persist($article);

                  // on donne des commentaires à l'article
                  for($k = 1; $k <= mt_rand(1, 3); $k++)
                  $comment = new Comment();

                  $now = new \DateTime();
                  $interval = $now->diff($article->getCreatedAt());
                  $days = $interval->days;
                  $mini = '-' . $days . 'days';  

                  $comment ->setAuthor($faker->name)
                                    ->setContent($faker->sentence)
                                    ->setCreatedAt($faker->dateTimeBetween($mini))
                                    ->setArticle($article);

                  $manager->persist($comment);

               }
            }
        $manager->flush();
    }
}
