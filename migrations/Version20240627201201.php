<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627201201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_version (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, number VARCHAR(255) NOT NULL, features LONGTEXT DEFAULT NULL, bugfix LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_VERSION (name, number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, game_master_id INT NOT NULL, creator_id INT NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, closed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_232B318CC1151A13 (game_master_id), INDEX IDX_232B318C61220EA6 (creator_id), UNIQUE INDEX UNIQ_IDENTIFIER_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metric (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_87D62EE3E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE protagonist (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, portrait_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, creator_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, story LONGTEXT DEFAULT NULL, level INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_200E1005E48FD905 (game_id), UNIQUE INDEX UNIQ_200E10051226EBF3 (portrait_id), INDEX IDX_200E10057E3C61F9 (owner_id), INDEX IDX_200E100561220EA6 (creator_id), UNIQUE INDEX UNIQ_IDENTIFIER_SLUG (slug, game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE protagonist_metric (id INT AUTO_INCREMENT NOT NULL, protagonist_id INT NOT NULL, metric_details_id INT NOT NULL, value INT NOT NULL, max INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3009F503CDF038AD (protagonist_id), INDEX IDX_3009F5039F1B5EF (metric_details_id), UNIQUE INDEX UNIQ_IDENTIFIER_METRIC (protagonist_id, metric_details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload (id INT AUTO_INCREMENT NOT NULL, uploader_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, size INT NOT NULL, mime_type VARCHAR(255) NOT NULL, original_file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_17BDE61F16678C77 (uploader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, session_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC1151A13 FOREIGN KEY (game_master_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE metric ADD CONSTRAINT FK_87D62EE3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE protagonist ADD CONSTRAINT FK_200E1005E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE protagonist ADD CONSTRAINT FK_200E10051226EBF3 FOREIGN KEY (portrait_id) REFERENCES upload (id)');
        $this->addSql('ALTER TABLE protagonist ADD CONSTRAINT FK_200E10057E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE protagonist ADD CONSTRAINT FK_200E100561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE protagonist_metric ADD CONSTRAINT FK_3009F503CDF038AD FOREIGN KEY (protagonist_id) REFERENCES protagonist (id)');
        $this->addSql('ALTER TABLE protagonist_metric ADD CONSTRAINT FK_3009F5039F1B5EF FOREIGN KEY (metric_details_id) REFERENCES metric (id)');
        $this->addSql('ALTER TABLE upload ADD CONSTRAINT FK_17BDE61F16678C77 FOREIGN KEY (uploader_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC1151A13');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C61220EA6');
        $this->addSql('ALTER TABLE metric DROP FOREIGN KEY FK_87D62EE3E48FD905');
        $this->addSql('ALTER TABLE protagonist DROP FOREIGN KEY FK_200E1005E48FD905');
        $this->addSql('ALTER TABLE protagonist DROP FOREIGN KEY FK_200E10051226EBF3');
        $this->addSql('ALTER TABLE protagonist DROP FOREIGN KEY FK_200E10057E3C61F9');
        $this->addSql('ALTER TABLE protagonist DROP FOREIGN KEY FK_200E100561220EA6');
        $this->addSql('ALTER TABLE protagonist_metric DROP FOREIGN KEY FK_3009F503CDF038AD');
        $this->addSql('ALTER TABLE protagonist_metric DROP FOREIGN KEY FK_3009F5039F1B5EF');
        $this->addSql('ALTER TABLE upload DROP FOREIGN KEY FK_17BDE61F16678C77');
        $this->addSql('DROP TABLE app_version');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE metric');
        $this->addSql('DROP TABLE protagonist');
        $this->addSql('DROP TABLE protagonist_metric');
        $this->addSql('DROP TABLE upload');
        $this->addSql('DROP TABLE user');
    }
}
