<?php

namespace App\DataFixtures;

use App\Entity\Achievement;
use App\Entity\Game;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{



    public function load(ObjectManager $manager)
    {
        
        for ($i = 0; $i < 20; $i++) {

            $user = new User;
            $user->setEmail('useremail' . $i . '@mail.com');
            $user->setUsername('user' . $i);
            $user->setPassword('root');
            $user->setAvatar('image' . $i);
            $user->setRoles(["ROLE"]);
            $user->setIsActive(1);
            $user->setCreatedAt(new \DateTime('now'));
            $manager->persist($user);

        } 
        for ($i = 0; $i < 5; $i++) {
            $achievement = new Achievement;
            $achievement->setPhrase('phrase'.$i);
            $achievement->setIcon('icon'.$i);
            $achievement->setCreatedAt(new \DateTime('now'));
            $manager->persist($achievement);
        
        }

        for ($i = 0; $i < 2; $i++) {
            $game = new Game ;
            $game->setName('name'.$i);
            $game->setIcon('icon'.$i);
            $manager->persist($game);
            
        }
        

        $manager->flush();
    }
}
