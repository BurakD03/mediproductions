<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207102542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Licence (id INT AUTO_INCREMENT NOT NULL, startedAt DATE NOT NULL, endedAt DATE NOT NULL, platform VARCHAR(255) NOT NULL, demo TINYINT(1) NOT NULL, state VARCHAR(255) NOT NULL, codeCrm VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, syliusOrder_id INT DEFAULT NULL, syliusProductVariant_id INT DEFAULT NULL, INDEX IDX_D217DFD4B49071CA (syliusOrder_id), INDEX IDX_D217DFD41121541B (syliusProductVariant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Licence ADD CONSTRAINT FK_D217DFD4B49071CA FOREIGN KEY (syliusOrder_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE Licence ADD CONSTRAINT FK_D217DFD41121541B FOREIGN KEY (syliusProductVariant_id) REFERENCES sylius_product_variant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Licence DROP FOREIGN KEY FK_D217DFD4B49071CA');
        $this->addSql('ALTER TABLE Licence DROP FOREIGN KEY FK_D217DFD41121541B');
        $this->addSql('DROP TABLE Licence');
    }
}
