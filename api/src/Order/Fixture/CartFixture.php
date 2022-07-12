<?php

declare(strict_types=1);

namespace App\Order\Fixture;

use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Auth\Model\User\Email;
use App\Auth\Model\User\Status;
use App\Auth\Model\User\User;
use App\Order\Model\Cart\Cart;
use App\Shop\Model\Product\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class CartFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            new Uuid('00000000-0000-0000-0000-000000000001'),
            new Email('user1@app.test'),
            Status::active()
        );

        $manager->persist($user);

        $cart = new Cart(
            new Uuid('00000000-0000-0000-0000-000000000001'),
            $user
        );

        $manager->persist($cart);

        $product1 = new Product(
            new Uuid('00000000-0000-0000-0000-000000000001'),
            'Product 1'
        );
        $product1->changeBalance(new Quantity(10));

        $manager->persist($product1);

        $product2 = new Product(
            new Uuid('00000000-0000-0000-0000-000000000002'),
            'Product 2'
        );
        $product2->changeBalance(new Quantity(10));

        $manager->persist($product2);

        $product3 = new Product(
            new Uuid('00000000-0000-0000-0000-000000000003'),
            'Product 3'
        );
        $product3->changeBalance(new Quantity(10));

        $manager->persist($product3);

        $cart->addCartItem(new Uuid('00000000-0000-0000-0000-000000000001'), $product1, new Quantity(1));

        $manager->flush();
    }
}
