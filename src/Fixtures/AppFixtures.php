<?php

namespace App\Fixtures;

use App\Entity\AppVersion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(){}

    public function load(ObjectManager $manager): void
    {
        $version = new AppVersion();
        $version
            ->setName('Vanish')
            ->setNumber("1.0.0")
            ->setFeatures("* Obre release");

        $manager->persist($version);

        $manager->flush();
    }
}
