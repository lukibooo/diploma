<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthService
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function register(string $email, string $plainPassword): ?User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function login(string $email, string $plainPassword, SessionInterface $session): ?User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $plainPassword)) {
            return null;
        }

        $session->set('user_id', $user->getId());
        return $user;
    }

    public function logout(SessionInterface $session): void
    {
        $session->remove('user_id');
    }

    public function getCurrentUser(SessionInterface $session): ?User
    {
        $id = $session->get('user_id');
        if (!$id) return null;

        return $this->em->getRepository(User::class)->find($id);
    }
}
