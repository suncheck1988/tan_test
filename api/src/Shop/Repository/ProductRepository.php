<?php

declare(strict_types=1);

namespace App\Shop\Repository;

use App\Application\Exception\NotFoundException;
use App\Application\Repository\AbstractRepository;
use App\Application\ValueObject\Uuid;
use App\Shop\Model\Product\Product;

final class ProductRepository extends AbstractRepository
{
    public function add(Product $model): void
    {
        $this->entityManager->persist($model);
    }

    public function get(Uuid $id): Product
    {
        $product = $this->entityRepository->find((string)$id);
        if ($product === null) {
            throw new NotFoundException(sprintf('Product with id %s not found', (string)$id));
        }

        /** @var Product $product */
        return $product;
    }

    public function fetchOneById(Uuid $id): ?Product
    {
        /** @var Product|null $model */
        $model = $this->entityRepository->findOneBy(['id' => $id]);

        return $model;
    }

    protected function getModelClassName(): string
    {
        return Product::class;
    }
}
