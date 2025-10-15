<?php

// src/Command/AddRoleUserCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRoleUserCommand extends Command
{
    protected static $defaultName = 'app:user:add-role';
    private $em;
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        parent::__construct();
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Ajoute un rôle à un utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l’utilisateur')
            ->addArgument('role', InputArgument::REQUIRED, 'Rôle à ajouter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $output->writeln("Utilisateur non trouvé.");
            return Command::FAILURE;
        }

        $roles = $user->getRoles();
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $user->setRoles($roles);
            $this->em->flush();
            $output->writeln("Rôle $role ajouté à $email.");
        } else {
            $output->writeln("L’utilisateur a déjà ce rôle.");
        }

        return Command::SUCCESS;
    }
}
