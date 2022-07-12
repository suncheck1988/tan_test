<?php

declare(strict_types=1);

namespace App\Order\Command\Cart\ChangeQuantityBatch;

use App\Application\Exception\DomainException;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Data\Flusher;
use App\Data\TransactionManager;
use App\Order\Model\Cart\Cart;
use App\Order\Repository\CartRepository;
use App\Shop\Repository\ProductRepository;
use Throwable;

class Handler
{
    private CartRepository $cartRepository;
    private ProductRepository $productRepository;
    private Flusher $flusher;
    private TransactionManager $transactionManager;

    public function __construct(
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        Flusher $flusher,
        TransactionManager $transactionManager
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->flusher = $flusher;
        $this->transactionManager = $transactionManager;
    }

    public function handle(Command $command): Cart
    {
        $this->transactionManager->beginTransaction();

        try {
            $cart = $this->cartRepository->get(new Uuid($command->getId()));

            foreach ($command->getItems() as $productDto) {
                $product = $this->productRepository->get(new Uuid($productDto->getProductId()));

                $cartItem = null;
                foreach ($cart->getCartItems() as $item) {
                    if ($item->getProduct() === $product) {
                        $cartItem = $item;
                        break;
                    }
                }

                $currentQuantity = null;
                $quantity = new Quantity($productDto->getQuantity());
                if ($cartItem === null) {
                    $cartItem = $cart->addCartItem(Uuid::generate(), $product, $quantity);
                } else {
                    $currentQuantity = $cartItem->getQuantity();
                    $cartItem->changeQuantity($quantity);
                }

                if ($quantity->getValue() === 0) {
                    $cart->removeCartItem($cartItem);
                } else {
                    if ($product->getBalance()->getValue() < $cartItem->getQuantity()->getValue()) {
                        if ($currentQuantity !== null) {
                            $cartItem->changeQuantity($currentQuantity);
                        } else {
                            if ($product->getBalance()->getValue() > 0) {
                                $balanceQuantity = new Quantity($product->getBalance()->getValue());
                                $cartItem->changeQuantity($balanceQuantity);
                            } else {
                                $cart->removeCartItem($cartItem);
                            }
                        }
                    }
                }

                $this->flusher->flush();
            }

            $this->transactionManager->commit();
        } catch (Throwable $e) {
            $this->transactionManager->rollback();

            throw new DomainException($e->getMessage());
        }

        return $cart;
    }
}
