<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class MeController
{
    public function __construct(private readonly Security $security)
    {
    }

    public function __invoke(): User
    {
        /** @var User $user */
        $user = $this->security->getUser();
        return $user;
    }
}