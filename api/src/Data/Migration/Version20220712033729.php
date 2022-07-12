<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220712033729 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE "user" (
                id UUID NOT NULL,
                email VARCHAR(255) NOT NULL,
                status SMALLINT NOT NULL,
                role SMALLINT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN "user".status IS \'(DC2Type:auth_user_status)\'');
        $this->addSql('COMMENT ON COLUMN "user".role IS \'(DC2Type:auth_user_role)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('
            CREATE TABLE cart (
                id UUID NOT NULL,
                user_id UUID NOT NULL,
                status SMALLINT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('COMMENT ON COLUMN cart.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart.status IS \'(DC2Type:order_cart_status)\'');
        $this->addSql('COMMENT ON COLUMN cart.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cart.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE cart_item (id UUID NOT NULL, cart_id UUID NOT NULL, product_id UUID NOT NULL, quantity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F0FE25271AD5CDBF ON cart_item (cart_id)');
        $this->addSql('CREATE INDEX IDX_F0FE25274584665A ON cart_item (product_id)');
        $this->addSql('COMMENT ON COLUMN cart_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart_item.cart_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart_item.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart_item.quantity IS \'(DC2Type:quantity)\'');
        $this->addSql('COMMENT ON COLUMN cart_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cart_item.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('
            CREATE TABLE product (
                id UUID NOT NULL,
                name VARCHAR(255) NOT NULL,
                balance INT NOT NULL,
                status SMALLINT NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('COMMENT ON COLUMN product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product.balance IS \'(DC2Type:quantity)\'');
        $this->addSql('COMMENT ON COLUMN product.status IS \'(DC2Type:shop_product_status)\'');
        $this->addSql('COMMENT ON COLUMN product.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN product.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart_item DROP CONSTRAINT FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP CONSTRAINT FK_F0FE25274584665A');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE product');
    }
}
