<?php

declare(strict_types=1);

namespace App\Shop\Model\Product;

use App\Application\Model\IdentifiableTrait;
use App\Application\Model\TimestampableTrait;
// use App\Application\ValueObject\Amount;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product')]
class Product
{
    use IdentifiableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: 'string')]
    private string $name;

    /*
    #[ORM\Column(type: 'amount')]
    private Amount $price;
    */

    #[ORM\Column(type: 'quantity')]
    private Quantity $balance;

    #[ORM\Column(type: 'shop_product_status')]
    private Status $status;

    public function __construct(
        Uuid $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
//        $this->price = new Amount(0);
        $this->balance = new Quantity(0);
        $this->status = Status::active();
        $this->createdAt = new DateTimeImmutable();
    }

    public function change(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

//    public function changePrice(Amount $price): void
//    {
//        if ($this->price->getValue() !== $price->getValue()) {
//            $this->price = $price;
//            $this->updatedAt = new DateTimeImmutable();
//        }
//    }

    public function changeBalance(Quantity $balance): void
    {
        $this->balance = $balance;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function active(): void
    {
        $this->status = Status::active();
    }

    public function inactive(): void
    {
        $this->status = Status::inactive();
    }

    public function getName(): string
    {
        return $this->name;
    }

//    public function getPrice(): Amount
//    {
//        return $this->price;
//    }

    public function getBalance(): Quantity
    {
        return $this->balance;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
