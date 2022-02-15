<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User;
        $user->setUsername('Thomas');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'shelby'
        ));
        $user->setRoles(['ROLE_ADMIN']);

        $user1 = new User;
        $user1->setUsername('John');
        $user1->setPassword($this->passwordEncoder->encodePassword(
            $user1,
            'shelby'
        ));

        $user2 = new User;
        $user2->setUsername('Arthur');
        $user2->setPassword($this->passwordEncoder->encodePassword(
            $user2,
            'shelby'
        ));

        $manager->persist($user);
        $manager->persist($user1);
        $manager->persist($user2);


        $manager->flush();
    }
}
