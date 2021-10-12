<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        // Instanciation du Faker (en français !)
        $faker = Faker\Factory::create('fr_FR');

        // Création d'un compte admin
        $admin = new User();

        // Hydratation du compte
        $admin
            ->setEmail('admin@a.a')
            ->setRegistrationDate( $faker->dateTimeBetween('-1 year', 'now') )
            ->setPseudonym('Batman')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword(
                $this->encoder->hashPassword($admin, 'aaaaaaaaA7/')
            )
        ;

        // Persistance de l'admin
        $manager->persist($admin);


        // Création de 10 comptes utilisateur
        for($i = 0; $i < 10; $i++){

            $user = new User();

            $user
                ->setEmail( $faker->email )
                ->setRegistrationDate( $faker->dateTimeBetween('-1 year', 'now') )
                ->setPseudonym( $faker->userName )
                // Même mot de passe pour tous les comptes
                ->setPassword( $this->encoder->hashPassword($user, 'aaaaaaaaA7/') )
            ;

            $manager->persist($user);

        }

        // Création de 200 articles
        for($i = 0; $i < 200; $i++){

            $article = new Article();

            $article
                ->setTitle( $faker->sentence(10) )
                ->setContent( $faker->paragraph(15) )
                ->setPublicationDate( $faker->dateTimeBetween('-1 year', 'now') )
                ->setAuthor( $admin )   // Batman sera l'auteur de tous les articles
            ;

            $manager->persist( $article );

        }

        // TODO: ajouter les commentaires


        // Sauvegarde des nouvelles entités dans la base de données
        $manager->flush();

    }
}

