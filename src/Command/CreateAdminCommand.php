<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'make:admin',
    description: 'Создание администратора',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Имя пользователя')
            ->addArgument('phone', InputArgument::REQUIRED, 'Телефон')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $phone = $input->getArgument('phone');
        $password = $input->getArgument('password');

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if ($existingUser) {
            $output->writeln("<error>Пользователь с телефоном {$phone} уже существует!</error>");

            $existingUser->setRoles([User::ROLE_ADMIN]);
            $this->entityManager->flush();
            $output->writeln("<info>Пользователю '{$existingUser->getName()}' назначены права администратора</info>");

            return Command::SUCCESS;
        }

        $user = new User();
        $user->setName($name);
        $user->setPhone($phone);
        $user->setRoles([User::ROLE_ADMIN]);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>Администратор создан:</info>');
        $output->writeln("  ID: {$user->getId()}");
        $output->writeln("  Имя: {$user->getName()}");
        $output->writeln("  Телефон: {$user->getPhone()}");

        return Command::SUCCESS;
    }
}
