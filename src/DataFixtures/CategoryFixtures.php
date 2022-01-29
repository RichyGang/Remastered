<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $category1 = new Category();
        $category2 = new Category();
        $category3 = new Category();
        $category4 = new Category();
        $category5 = new Category();

        $category1->setName('Bien');
        $category2->setName('Service');
        $category3->setName('Alimentaire');
        $category4->setName('Vie');
        $category5->setName('Énergie');

        $this->setReference('category3', $category3);
        $this->setReference('category2', $category2);
        $this->setReference('category1', $category1);
        $this->setReference('category4', $category4);
        $this->setReference('category5', $category4);

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->persist($category5);

        $manager->flush();

    }
}
