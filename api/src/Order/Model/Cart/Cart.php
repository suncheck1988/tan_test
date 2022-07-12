<?php

declare(strict_types=1);

namespace App\Order\Model\Cart;

use App\Application\Model\IdentifiableTrait;
use App\Application\Model\TimestampableTrait;
use App\Application\ValueObject\Amount;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Auth\Model\User\User;
use App\Shop\Model\Product\Product;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'cart')]
class Cart
{
    use IdentifiableTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'order_cart_status')]
    private Status $status;

    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart', cascade: ['all'], orphanRemoval: true)]
    private Collection $cartItems;

    public function __construct(Uuid $id, User $user)
    {
        $this->id = $id;
        $this->user = $user;
        $this->status = Status::active();
        $this->cartItems = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function addCartItem(Uuid $id, Product $product, Quantity $quantity): CartItem
    {
        foreach ($this->getCartItems() as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                throw new InvalidArgumentException('Current product already added in cart');
            }
        }

        $cartItem = new CartItem($id, $this, $product, $quantity);
        $this->cartItems->add($cartItem);
        $this->updatedAt = new DateTimeImmutable();

        return $cartItem;
    }

    public function removeCartItem(CartItem $cartItem): void
    {
        $this->cartItems->removeElement($cartItem);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function clear(): void
    {
        $this->cartItems->clear();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     * @return CartItem[]
     */
    public function getCartItems(): array
    {
        return $this->cartItems->toArray();
    }

//    public function getTotalAmount(): Amount
//    {
//        $totalPrice = 0;
//        foreach ($this->getCartItems() as $cartItem) {
//            $totalPrice = $totalPrice +
//                ($cartItem->getQuantity()->getValue() * $cartItem->getProduct()->getPrice()->getValue());
//        }
//
//        return new Amount($totalPrice);
//    }
}
