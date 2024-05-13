<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513203846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload ADD file_name VARCHAR(255) NOT NULL, ADD original_file_name VARCHAR(255) NOT NULL, DROP name, DROP original_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload ADD name VARCHAR(255) NOT NULL, ADD original_name VARCHAR(255) NOT NULL, DROP file_name, DROP original_file_name');
    }
}
