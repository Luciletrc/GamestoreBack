<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712122031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_store DROP FOREIGN KEY FK_5E0B232B4584665A');
        $this->addSql('ALTER TABLE product_store DROP FOREIGN KEY FK_5E0B232BB092A811');
        $this->addSql('DROP TABLE product_store');
        $this->addSql('ALTER TABLE images CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADFCDAEAAA');
        $this->addSql('DROP INDEX IDX_D34A04ADFCDAEAAA ON product');
        $this->addSql('ALTER TABLE product DROP order_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_store (product_id INT NOT NULL, store_id INT NOT NULL, INDEX IDX_5E0B232B4584665A (product_id), INDEX IDX_5E0B232BB092A811 (store_id), PRIMARY KEY(product_id, store_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_store ADD CONSTRAINT FK_5E0B232B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_store ADD CONSTRAINT FK_5E0B232BB092A811 FOREIGN KEY (store_id) REFERENCES store (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images CHANGE product_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD order_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADFCDAEAAA FOREIGN KEY (order_id_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04ADFCDAEAAA ON product (order_id_id)');
    }
}
