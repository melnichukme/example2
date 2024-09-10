<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Region;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{
    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
        $this->loadCountry($manager);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function loadUser(ObjectManager $manager): void
    {
        foreach (
            $this->getUserData(
            ) as [$userName, $roles, $password, $firstName, $lastName, $thirdName, $createdAt, $photoPath, $isActive, $locale]
        ) {
            $entity = new User();
            $entity->setUsername($userName);
            $entity->setRoles($roles);
            $entity->setPassword($this->hasher->hashPassword($entity, $password));
            $entity->setFirstName($firstName);
            $entity->setLastName($lastName);
            $entity->setThirdName($thirdName);
            $entity->setCreatedAt(DateTime::createFromFormat("Y-m-d", $createdAt));
            $entity->setPhotoPath($photoPath);
            $entity->setIsActive($isActive);
            $entity->setLocale($locale);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function loadCountry(ObjectManager $manager): void
    {
        $region = new Region();
        $region->setTitle('Unknown');
        $manager->persist($region);

        foreach ($this->getCountryData() as $countryData) {
            $country = new Country();
            $country->setName($countryData['directory'] ?? $countryData['en']);
            $country->setCode($countryData['iso']);
            $country->setRegion($region);
            $manager->persist($country);
        }


        $manager->flush();
    }

    /**
     * @return array[]
     */
    private function getUserData(): array
    {
        return [
            // $data = [$userName, $roles, $password, $firstName, $lastName, $thirdName, $createdAt, $photoPath, $isActive, $locale];
            [
                'admin',
                [User::ROLE_ADMIN],
                '123123',
                'firstName',
                'lastName',
                null,
                '2023-06-01',
                null,
                1,
                'ru'
            ]
        ];
    }

    /**
     * @return array
     */
    private function getCountryData(): array
    {
        return [
            [
                "iso"=> "JP",
                "ru"=> "Япония",
                "en"=> "Japan",
                "directory"=> "Japan"
            ],
            [
                "iso"=> "JM",
                "ru"=> "Ямайка",
                "en"=> "Jamaica",
                "directory"=> "Jamaica"
            ],
            [
                "iso"=> "SS",
                "ru"=> "Южный Судан",
                "en"=> "South Sudan",
                "directory"=> "the Republic of South Sudan"
            ]
        ];
    }
}
