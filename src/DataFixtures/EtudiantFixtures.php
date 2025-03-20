<?php

namespace App\DataFixtures;

use App\Entity\Etudiant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtudiantFixtures extends Fixture
{

    // Inject the password hasher service
    public function __construct( )
    {
    }
    public function load(ObjectManager $manager): void
    {
        $etudiant = new Etudiant();
        $etudiant->setEmail('mehdi@baggar.com');
        $etudiant->setPassword('mehdi123M');
        $manager->persist($etudiant);

        $manager->flush();
    }
}
