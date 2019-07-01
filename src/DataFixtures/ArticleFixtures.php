<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
            for($i = 1; $i <= 12; $i++){
               $article = new Article();
               $article->setTitre("Gateaux $i")
                           ->setDifficulte("facile")
                           ->setCreatedAt(new \DateTime())
                           ->setModifiedAt(new \DateTime())
                           ->setImage("https://loremflickr.com/300/200/cake")
                           ->setIngredients("<p>3oeuf, 200g de farine, 100g de sucre</p>")
                           ->setPreparation("20 min")
                           ->setCuisson("30 min")
                           ->setRecette("<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus nihil molestias nulla laudantium dolore et beatae culpa ad, vitae quia aliquam impedit nobis labore! Soluta rem dolorem delectus quasi, porro beatae dolore labore perspiciatis eum tempora, at suscipit ex similique esse consectetur ut sed voluptatibus iste necessitatibus sit ipsum. Illum.</p>");

               $manager->persist($article);

            }
        $manager->flush();
    }
}
