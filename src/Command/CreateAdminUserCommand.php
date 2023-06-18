<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'CreateAdminUserCommand',
    description: 'Add a short description for your command',
)]
class CreateAdminUserCommand extends Command
{
    private $userPasswordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        
        parent::__construct();
    }
    

    protected function configure(): void
    {
            $this
        ->setName('app:create-admin-user')
        ->setDescription('Create an admin user')
        ->addArgument('firstname', InputArgument::REQUIRED, 'Le prénom : ')
        ->addArgument('lastname', InputArgument::REQUIRED, 'Le nom de famille :')
        ->addArgument('password', InputArgument::REQUIRED, 'Le mot de passe :')
        ->addArgument('email', InputArgument::REQUIRED, 'Adresse email : ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        // Création de l'utilisateur admin
        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );


        // Persiste et enregistre l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Admin user created successfully!');
    
        return Command::SUCCESS;

    }
}
