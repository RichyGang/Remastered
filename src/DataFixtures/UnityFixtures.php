<?php

namespace App\DataFixtures;

use App\Entity\Unity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UnityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
//        Définition des categories de unités de base
         $unity1 = new Unity();
         $unity1->setDegree(0);
         $unity1->setGrandeur(null);
         $unity1->setName("Longueur");
         $unity1->setSymbol('');
         $manager->persist($unity1);

        $unity2 = new Unity();
        $unity2->setDegree(0);
        $unity2->setGrandeur(null);
        $unity2->setName("Surface");
        $unity2->setSymbol('');
        $manager->persist($unity2);

        $unity3 = new Unity();
        $unity3->setDegree(0);
        $unity3->setGrandeur(null);
        $unity3->setName("Volume");
        $unity3->setSymbol('');
        $manager->persist($unity3);

        $unity4 = new Unity();
        $unity4->setDegree(0);
        $unity4->setGrandeur(null);
        $unity4->setName("Masse");
        $unity4->setSymbol('');
        $manager->persist($unity4);

        $unity5 = new Unity();
        $unity5->setDegree(0);
        $unity5->setGrandeur(null);
        $unity5->setName("Temps");
        $unity5->setSymbol('');
        $manager->persist($unity5);

        $manager->flush();
    }
}
