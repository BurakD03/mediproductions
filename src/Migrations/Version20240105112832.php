<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105112832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE SubscriptionPaymentSchedule (id INT AUTO_INCREMENT NOT NULL, subscription_id INT NOT NULL, scheduledDate DATETIME NOT NULL, fulfilledDate DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_C1B87459A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SubscriptionPaymentSchedule ADD CONSTRAINT FK_C1B87459A1887DC FOREIGN KEY (subscription_id) REFERENCES Subscription (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE SubscriptionPaymentSchedule DROP FOREIGN KEY FK_C1B87459A1887DC');
        $this->addSql('DROP TABLE SubscriptionPaymentSchedule');
    }
}
