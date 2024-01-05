<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240103141853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Subscription (id INT AUTO_INCREMENT NOT NULL, state VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, currentPayment INT NOT NULL, totalPayment INT NOT NULL, syliusOrder_id INT DEFAULT NULL, INDEX IDX_BBF7BF2BB49071CA (syliusOrder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Subscription ADD CONSTRAINT FK_BBF7BF2BB49071CA FOREIGN KEY (syliusOrder_id) REFERENCES sylius_order (id)');
        $this->addSql('ALTER TABLE licence ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_D217DFD49A1887DC FOREIGN KEY (subscription_id) REFERENCES Subscription (id)');
        $this->addSql('CREATE INDEX IDX_D217DFD49A1887DC ON licence (subscription_id)');
        $this->addSql('ALTER TABLE sylius_product_variant CHANGE durationValue durationValue INT NOT NULL, CHANGE durationUnit durationUnit VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Licence DROP FOREIGN KEY FK_D217DFD49A1887DC');
        $this->addSql('ALTER TABLE Subscription DROP FOREIGN KEY FK_BBF7BF2BB49071CA');
        $this->addSql('DROP TABLE Subscription');
        $this->addSql('DROP INDEX IDX_D217DFD49A1887DC ON Licence');
        $this->addSql('ALTER TABLE Licence DROP subscription_id');
        $this->addSql('ALTER TABLE sylius_product_variant CHANGE durationValue durationValue INT DEFAULT NULL, CHANGE durationUnit durationUnit VARCHAR(255) DEFAULT NULL');
    }
}
