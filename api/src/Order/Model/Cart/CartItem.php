<?php

declare(strict_types=1);

namespace App\Order\Model\Cart;

use App\Application\Model\IdentifiableTrait;
use App\Application\Model\TimestampableTrait;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Shop\Model\Product\Product;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cart_item')]
class CartItem
{
    use IdentifiableTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'id', nullable: false)]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'quantity')]
    private Quantity $quantity;

    public function __construct(Uuid $id, Cart $cart, Product $product, Quantity $quantity)
    {
        $this->id = $id;
        $this->cart = $cart;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->createdAt = new DateTimeImmutable();
    }

    public function changeQuantity(Quantity $quantity): void
    {
        $this->quantity = $quantity;
        $this->updatedAt = new DateTimeImmutable();
        $this->cart->triggerUpdatedAt();
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }
}
