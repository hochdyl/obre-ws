<?php

namespace App\Fixtures;

use App\Entity\AppVersion;
use App\Entity\Game;
use App\Entity\User;
use App\Service\UserPasswordService;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordService $userPasswordService){}

    public function load(ObjectManager $manager): void
    {
        $version = new AppVersion();
        $version
            ->setName('Vanish')
            ->setNumber("0.1.0");

        $manager->persist($version);

        $user = new User();
        $user
            ->setUsername('admin')
            ->setPassword('admin');
        $this->userPasswordService->hashPassword($user);
        $user->generateSessionToken();

        $manager->persist($user);

        $game = new Game();
        $game
            ->setTitle("Fixture game")
            ->setSlug('fixture-game')
            ->setStartedAt(new DateTimeImmutable())
            ->setOwner($user)
            ->setCreator($user)
            ->setClosed(false);

        $manager->persist($game);

        $manager->flush();
    }
}
