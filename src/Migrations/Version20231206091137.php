<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206091137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE office ADD createdAt DATE NOT NULL');
        $this->addSql('ALTER TABLE office RENAME INDEX idx_73fd6e349d86650f TO IDX_73FD6E34A76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Office DROP createdAt');
        $this->addSql('ALTER TABLE Office RENAME INDEX idx_73fd6e34a76ed395 TO IDX_73FD6E349D86650F');
    }
}
