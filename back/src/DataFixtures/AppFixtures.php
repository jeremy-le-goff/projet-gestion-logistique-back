<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use App\DataFixtures\Provider\Projet14;
use App\Entity\Brands;
use App\Entity\Categories;
use App\Entity\Organizations;
use App\Entity\Products;
use App\Entity\Structures;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    //We hash the user's password
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Faker Instantiation in French
        $faker = Factory::create('fr_FR');
        
        // We provide Projet14 in Faker
        $faker->addProvider(new Projet14());
        
        // Creating organizations
        for ($i=0; $i < 10; $i++) {
            $organization = new Organizations();
            $organization->setName($faker->unique()->organizationRandom());
            $organization->setEmail($faker->safeEmail());
            $organization->setPhoneNumber($faker->phoneNumber());
            $organization->setAddress($faker->streetAddress());
            $organization->setType($faker->companySuffix());
            // For the siren, I removed the spaces to avoid an SQL error because the data was too long
            $organization->setSiren(str_replace(' ','',$faker->siren()));
            $organization->setStatus($faker->numberBetween(0,2));
            // Creation of an immutable date format with a +/- one-week interval
            $organization->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));
            
            $manager->persist($organization);
            // Create a table to randomly generate the affiliation of a structure to an organization.
            $organizations[] = $organization;
        }
        
        // Creating strutures
        for ($i=0; $i < 10; $i++) {
            $structure = new Structures();
            // Random parent generation
            $randomOrganization = $faker->randomElement($organizations);
            $structure->setOrganizations($randomOrganization);
            $structure->setName($faker->unique()->company());
            // For the siret, I removed the spaces to avoid an SQL error because the data was too long.
            $structure->setSiret(str_replace(' ','',$faker->siret()));
            $structure->setStatus($faker->boolean());
            // Creation of an immutable date format with a +/- one-week interval
            $structure->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));
            
            $manager->persist($structure);
            $structures[] = $structure;
        }
        
        // Creating categories
        for ($i=0; $i < 8; $i++) { 
            $category = new Categories();
            $category->setName($faker->unique()->categoryRandom());
            // Creation of an immutable date format with a +/- one-week interval
            $category->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));
            
            $manager->persist($category);
            $categories[] = $category;
        }

        // Creating brands
        for ($i=0; $i < 19; $i++) { 
            $brand = new Brands();
            $brand->setName($faker->unique()->brandRandom());
            // Creation of an immutable date format with a +/- one-week interval
            $brand->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));
            
            $manager->persist($brand);
            $brands[] = $brand;

        }
        
        // On crée les produits
        for ($i=0; $i < 49; $i++) { 
                $product = new Products();
                $product->setName($faker->unique()->productRandom());
                $product->setDescription($faker->text);
                $product->setPicture($faker->imageUrl());
                $product->setPrice($faker->randomFloat(2, 1, 100));
                $product->setConservationType($faker->conservationTypeRandom());
                $product->setWeight($faker->numberBetween(50, 5000));
                $product->setConditioning($faker->conditioningRandom());
                $product->setQuantity($faker->numberBetween(null,1000));
                // Creation of an immutable date format with a +/- one-month interval
                $product->setExpirationDate(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '+1 month')));
                $product->setean13($faker->ean13());
                // Creation of an immutable date format with a +/- one-week interval
                $product->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));
                
                // Random stucture relation
                $randomStructures = $faker->randomElement($structures);
                $product->setStructures($randomStructures);

                // Random category relation
                $randomCategories = $faker->randomElement($categories);
                $product->setCategories($randomCategories);

                // Random brand relation
                $randomBrands = $faker->randomElement($brands);
                $product->setBrands($randomBrands);

            
                $manager->persist($product);
                $products[]=$product;
            }            
            
            

            //We create unique users
            for ($i = 0; $i < 5; $i++){

                
                $providerUser = $faker->unique()->userRandom();

                $user = new User();
                $user->setFirstname($faker->unique()->firstName());
                $user->setLastname($faker->unique()->lastName());
                $user->setEmail($providerUser["email"]);
                $user->setPassword($this->passwordHasher->hashPassword($user,$providerUser["password"]));
                $user->setPhoneNumber($faker->unique()->phoneNumber());
                $user->setRoles($providerUser["role"]);
                $user->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 week', '+1 week')));

                $user->setStatus($faker->statusRandom());
               
                //Erreur à modifier
                $randomStructures = $faker->randomElement($structures);
                $user->setStructures($randomStructures);
                $user->setOrganizations($randomStructures->getOrganizations());   

                
                $manager->persist($user);

            }

            $manager->flush();
        }
    }
