<?php

declare(strict_types=1);

namespace App\Order\Repository;

use App\Application\Exception\NotFoundException;
use App\Application\Repository\AbstractRepository;
use App\Application\ValueObject\Uuid;
use App\Order\Model\Cart\Cart;

class CartRepository extends AbstractRepository
{
    public function add(Cart $model): void
    {
        $this->entityManager->persist($model);
    }

    public function get(Uuid $id): Cart
    {
        /** @var Cart|null $model */
        $model = $this->entityRepository->find($id);
        if ($model === null) {
            throw new NotFoundException(sprintf('Cart with id %s not found', (string)$id));
        }

        return $model;
    }

    protected function getModelClassName(): string
    {
        return Cart::class;
    }
}
