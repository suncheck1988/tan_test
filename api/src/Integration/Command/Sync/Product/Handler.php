<?php

declare(strict_types=1);

namespace App\Integration\Command\Sync\Product;

use App\Application\ValueObject\Amount;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Data\Flusher;
use App\Shop\Model\Product\Product;
use App\Shop\Repository\ProductRepository;

class Handler
{
    public function __construct(
        private ProductRepository $productRepository,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): Product
    {
        $id = new Uuid($command->getId());
        $name = $command->getName();

        $product = $this->productRepository->fetchOneById($id);
        if ($product !== null) {
            $product->change($name);
        } else {
            $product = new Product(
                Uuid::generate(),
                $name
            );
            $this->productRepository->add($product);
        }

//        $product->changePrice(Amount::fromRub($command->getPrice()));
        $product->changeBalance(new Quantity($command->getBalance()));

        if ($command->isActive()) {
            $product->active();
        } else {
            $product->inactive();
        }

        $this->flusher->flush();

        return $product;
    }
}
