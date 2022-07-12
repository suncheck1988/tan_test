<?php

declare(strict_types=1);

namespace App\Auth\Repository;

use App\Application\Exception\NotFoundException;
use App\Application\Repository\AbstractRepository;
use App\Application\ValueObject\Uuid;
use App\Auth\Model\User\User;

final class UserRepository extends AbstractRepository
{
    public function get(Uuid $id): User
    {
        $user = $this->entityRepository->find((string)$id);
        if ($user === null) {
            throw new NotFoundException(sprintf('User with id %s not found', (string)$id));
        }

        /** @var User $user */
        return $user;
    }

    protected function getModelClassName(): string
    {
        return User::class;
    }
}
