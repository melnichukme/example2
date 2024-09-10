<?php

namespace App\Command\Fill;

use App\Entity\User;
use App\Enums\LocaleEnum;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app.fill-admin-account',
    description: 'Fill admin account',
)]
class FillAdminAccountCommand extends Command
{
    /**
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $hasher
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        protected UserRepository              $userRepository,
        protected UserPasswordHasherInterface $hasher,
        protected ParameterBagInterface       $parameterBag
    )
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = 'admin';
        $password = bin2hex(random_bytes(12));

        if ($user = $this->userRepository->findOneBy(['username' => $username])) {
            $user->setPassword($this->hasher->hashPassword($user, $password));

            $io->success('Пароль администратора изменен. Логин:' . $username . ' Пароль:' . $password);
        } else {
            $user = new User();
            $user->setUsername('admin');
            $user->setRoles([User::ROLE_ADMIN]);
            $user->setPassword($this->hasher->hashPassword($user, $password));
            $user->setCreatedAt(new \DateTime());
            $user->setLocale(LocaleEnum::EN->value);

            $io->success('Аккаунт администратора создан. Логин:' . $username . ' Пароль:' . $password);
        }

        $this->userRepository->save($user, true);

        return Command::SUCCESS;
    }
}
