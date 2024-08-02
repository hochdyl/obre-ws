<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802195823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '1.1.0 - Add unitType to ProtagonistMetric';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE protagonist_metric ADD unit_type INT DEFAULT 1 NOT NULL AFTER max');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE protagonist_metric DROP unit_type');
    }
}
