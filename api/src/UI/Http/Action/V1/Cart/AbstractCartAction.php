<?php

declare(strict_types=1);

namespace App\UI\Http\Action\V1\Cart;

use App\Integration\Service\Sync\Api;
use App\Order\Model\Cart\Cart;
use App\UI\Http\Action\AbstractAction;

abstract class AbstractCartAction extends AbstractAction
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    protected function serializeItem(Cart $model): array
    {
        $totalAmount = 0;
        $items = [];

        foreach ($model->getCartItems() as $cartItem) {
            $productDto = $this->api->getProduct((string)$cartItem->getProduct()->getId());

            $items[] = [
                'id' => (string)$cartItem->getId(),
                'productId' => (string)$cartItem->getProduct()->getId(),
                'quantity' => $cartItem->getQuantity()->getValue(),
                'amount' => $productDto->getPrice()->toRub(),
            ];

            $totalAmount += $cartItem->getQuantity()->getValue() * $productDto->getPrice()->toRub();
        }

        return [
            'id' => (string)$model->getId(),
            'items' => $items,
            'totalAmount' => $totalAmount,
        ];
    }
}
