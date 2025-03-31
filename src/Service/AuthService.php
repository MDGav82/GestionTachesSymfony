<?php
namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AuthService
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function verifyPassword(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}