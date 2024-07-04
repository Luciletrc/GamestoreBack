<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704144329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, opening_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FF5758777E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758777E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stores DROP FOREIGN KEY FK_D5907CCC7E3C61F9');
        $this->addSql('DROP TABLE stores');
        $this->addSql('ALTER TABLE images ADD product_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ADE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6ADE18E50B ON images (product_id_id)');
        $this->addSql('ALTER TABLE `order` ADD user_id_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP product_id, DROP quantity, DROP name, DROP uptadet_at');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993989D86650F ON `order` (user_id_id)');
        $this->addSql('ALTER TABLE order_status ADD order_id_id INT NOT NULL, DROP name, CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE order_status ADD CONSTRAINT FK_B88F75C9FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B88F75C9FCDAEAAA ON order_status (order_id_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADFACB6020');
        $this->addSql('DROP INDEX IDX_D34A04ADFACB6020 ON product');
        $this->addSql('ALTER TABLE product ADD order_id_id INT DEFAULT NULL, DROP genre, DROP image, CHANGE stocks_id category_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9777D11E FOREIGN KEY (category_id_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADFCDAEAAA FOREIGN KEY (order_id_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD9777D11E ON product (category_id_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADFCDAEAAA ON product (order_id_id)');
        $this->addSql('ALTER TABLE stock ADD product_id_id INT NOT NULL, ADD quantity SMALLINT NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_4B365660DE18E50B ON stock (product_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9777D11E');
        $this->addSql('CREATE TABLE stores (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, opening_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D5907CCC7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stores ADD CONSTRAINT FK_D5907CCC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF5758777E3C61F9');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE store');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6ADE18E50B');
        $this->addSql('DROP INDEX IDX_E01FBE6ADE18E50B ON images');
        $this->addSql('ALTER TABLE images DROP product_id_id');
        $this->addSql('ALTER TABLE order_status DROP FOREIGN KEY FK_B88F75C9FCDAEAAA');
        $this->addSql('DROP INDEX UNIQ_B88F75C9FCDAEAAA ON order_status');
        $this->addSql('ALTER TABLE order_status ADD name VARCHAR(255) NOT NULL, DROP order_id_id, CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADFCDAEAAA');
        $this->addSql('DROP INDEX UNIQ_D34A04AD9777D11E ON product');
        $this->addSql('DROP INDEX IDX_D34A04ADFCDAEAAA ON product');
        $this->addSql('ALTER TABLE product ADD genre VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, DROP order_id_id, CHANGE category_id_id stocks_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADFACB6020 FOREIGN KEY (stocks_id) REFERENCES stock (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04ADFACB6020 ON product (stocks_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989D86650F');
        $this->addSql('DROP INDEX IDX_F52993989D86650F ON `order`');
        $this->addSql('ALTER TABLE `order` ADD product_id VARCHAR(255) NOT NULL, ADD quantity SMALLINT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD uptadet_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP user_id_id, DROP updated_at');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660DE18E50B');
        $this->addSql('DROP INDEX IDX_4B365660DE18E50B ON stock');
        $this->addSql('ALTER TABLE stock ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP product_id_id, DROP quantity');
    }
}
