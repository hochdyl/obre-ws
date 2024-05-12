<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511215824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_232B318CB03A8386 ON game (created_by_id)');
        $this->addSql('ALTER TABLE protagonist ADD created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE protagonist ADD CONSTRAINT FK_200E1005B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_200E1005B03A8386 ON protagonist (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CB03A8386');
        $this->addSql('DROP INDEX IDX_232B318CB03A8386 ON game');
        $this->addSql('ALTER TABLE game DROP created_by_id');
        $this->addSql('ALTER TABLE protagonist DROP FOREIGN KEY FK_200E1005B03A8386');
        $this->addSql('DROP INDEX IDX_200E1005B03A8386 ON protagonist');
        $this->addSql('ALTER TABLE protagonist DROP created_by_id');
    }
}
