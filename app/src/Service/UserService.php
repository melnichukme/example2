<?php

namespace App\Service;

use App\Dto\User\UserCreateDto;
use App\Entity\User;
use App\Enums\LocaleEnum;
use App\Filter\UserFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\LocaleSwitcher;

class UserService
{
    /**
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $hasher
     * @param ParameterBagInterface $parameterBag
     * @param Security $security
     * @param LocaleSwitcher $localeSwitcher
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $hasher,
        protected ParameterBagInterface $parameterBag,
        protected Security $security,
        protected LocaleSwitcher $localeSwitcher
    ) {
    }

    /**
     * @param UserFilter $filter
     * @return array
     */
    public function getList(UserFilter $filter): array
    {
        return $this->userRepository->getAllWithFilter($filter);
    }

    /**
     * @param $id
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getById($id): ?User
    {
        $user = $this->userRepository->getById($id);

        if (is_null($user)) {
            throw new NotFoundHttpException('Not found.');
        }

        return $user;
    }

    /**
     * @param UserCreateDto $dto
     * @return User
     */
    public function create(UserCreateDto $dto): User
    {
        $user = new User();
        $user->setUsername($dto->username);
        $user->setRoles([$dto->role]);
        $user->setPassword($this->hasher->hashPassword($user, $dto->password));
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setThirdName($dto->thirdName);

        $user->setLocale(LocaleEnum::RU->value);
        $user->setCreatedAt(new \DateTime('now'));

        $this->userRepository->save($user, true);

        return $user;
    }

    /**
     * @param int $id
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function update(int $id, array $data): void
    {
        $user = $this->userRepository->find($id);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        if (isset($data['password'])) {
            $user->setPassword($this->hasher->hashPassword($user, $data['password']));
        }

        if (isset($data['first_name'])) {
            $user->setFirstName($data['first_name'] ?? null);
        }

        if (isset($data['last_name'])) {
            $user->setLastName($data['last_name'] ?? null);
        }

        if (isset($data['third_name'])) {
            $user->setThirdName($data['third_name'] ?? null);
        }

        if (isset($data['locale'])) {
            $this->localeSwitcher->setLocale($data['locale']);
            $user->setLocale($data['locale']);
        }

        $this->userRepository->save($user, true);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function uploadPhoto(UploadedFile $file): string
    {
        /** @var User $user */
        $user = $this->security
            ->getUser();

        if (!is_null($path = $user->getPhotoPath())) {
            $filesystem = new Filesystem();
            $filesystem->remove($path);
        }

        $uploadPath = $this->parameterBag->get('files.user_photo.path');
        $uploadFullPath = $this->parameterBag->get('files.user_photo.path_full');
        $filename = md5(uniqid()) . '.' . $file->guessExtension();

        // TODO: ImageOptimizer crop or resize в будущем, если будет необходимо

        $file->move(
            $uploadFullPath,
            $filename
        );

        $fullPath = $uploadPath . '/' . $filename;

        $user->setPhotoPath($fullPath);
        $this->userRepository->save($user, true);

        return $this->parameterBag->get('app.base_url') . '/' . $fullPath;
    }

    /**
     * @param int $id
     * @return void
     */
    public function changeActive(int $id): void
    {
        $user = $this->userRepository->find($id);
        $user->setIsActive(!$user->getIsActive());

        $this->userRepository->save($user, true);
    }
}