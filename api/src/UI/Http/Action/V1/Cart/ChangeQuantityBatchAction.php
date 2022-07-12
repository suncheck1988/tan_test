<?php

declare(strict_types=1);

namespace App\UI\Http\Action\V1\Cart;

use App\Order\Command\Cart\ChangeQuantityBatch\Command;
use App\Order\Command\Cart\ChangeQuantityBatch\ItemDto;
use App\Order\Model\Cart\Cart;
use App\UI\Http\ParamsExtractor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ChangeQuantityBatchAction extends AbstractCartAction
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->deserialize($request);
        $this->validator->validate($command);

        /** @var Cart $cart */
        $cart = $this->bus->handle($command);

        $data = $this->serializeItem($cart);

        return $this->asJson($data);
    }

    private function deserialize(ServerRequestInterface $request): Command
    {
        $paramsExtractor = ParamsExtractor::fromRequest($request);

        $items = [];
        foreach ($paramsExtractor->getArray('products') as $item) {
            $items[] = new ItemDto(
                $item->getString('productId'),
                $item->getInt('quantity')
            );
        }

        return new Command(
            '00000000-0000-0000-0000-000000000001',
            $items
        );
    }
}
