<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215113359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brands (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organizations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, phone_number VARCHAR(20) NOT NULL, address VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, siren VARCHAR(9) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, structures_id INT DEFAULT NULL, categories_id INT NOT NULL, brands_id INT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(300) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, conservation_type VARCHAR(100) NOT NULL, weight INT NOT NULL, conditioning VARCHAR(100) NOT NULL, quantity INT DEFAULT NULL, expiration_date DATETIME DEFAULT NULL, ean13 VARCHAR(13) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B3BA5A5A9D3ED38D (structures_id), INDEX IDX_B3BA5A5AA21214B7 (categories_id), INDEX IDX_B3BA5A5AE9EEC0C7 (brands_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structures (id INT AUTO_INCREMENT NOT NULL, organizations_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, siret VARCHAR(14) DEFAULT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5BBEC55A86288A55 (organizations_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, organizations_id INT DEFAULT NULL, structures_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, phone_number VARCHAR(20) NOT NULL, status TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64986288A55 (organizations_id), INDEX IDX_8D93D6499D3ED38D (structures_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A9D3ED38D FOREIGN KEY (structures_id) REFERENCES structures (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AE9EEC0C7 FOREIGN KEY (brands_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE structures ADD CONSTRAINT FK_5BBEC55A86288A55 FOREIGN KEY (organizations_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986288A55 FOREIGN KEY (organizations_id) REFERENCES organizations (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499D3ED38D FOREIGN KEY (structures_id) REFERENCES structures (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A9D3ED38D');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA21214B7');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AE9EEC0C7');
        $this->addSql('ALTER TABLE structures DROP FOREIGN KEY FK_5BBEC55A86288A55');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986288A55');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499D3ED38D');
        $this->addSql('DROP TABLE brands');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE organizations');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE structures');
        $this->addSql('DROP TABLE user');
    }
}
