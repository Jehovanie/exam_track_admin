<?php

namespace App\DataFixtures;

use App\Entity\Exam;
use App\Entity\User;
use App\Dto\ExamInput;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setPassword(password: $this->passwordHasher->hashPassword($user, 'admin'));
        
        $manager->persist($user);


        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword(password: $this->passwordHasher->hashPassword($user, 'secret'));
            $manager->persist($user);
        }

        $manager->flush();


        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            // 1. Créer le DTO
            $input = new ExamInput();
            $input->studentName = $faker->name();
            $input->location = $faker->city();
            $input->status = $faker->randomElement(['in_search_place','confirm','in_organised', 'canceled']);
            $input->date = $faker->dateTimeBetween('+1 days', '+1 month');
            $input->time = $faker->dateTimeBetween('09:00', '18:00');

            // 2. Mapper le DTO vers l’entité
            $exam = new Exam();
            $exam->setStudentName($input->studentName);
            $exam->setLocation($input->location);
            $exam->setStatus($input->status);
            $exam->setDate($input->date);
            $exam->setTime($input->time);

            $manager->persist($exam);
        }

        $manager->flush();
    }
}
