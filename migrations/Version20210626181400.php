<?php

declare(strict_types=1);

namespace Parser;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210626181400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Player (id INT AUTO_INCREMENT NOT NULL, nickname VARCHAR(255) NOT NULL, guild VARCHAR(255) NOT NULL, `rank` INT NOT NULL, class VARCHAR(255) DEFAULT NULL, spec VARCHAR(255) DEFAULT NULL, gear INT DEFAULT NULL, rio DOUBLE PRECISION DEFAULT NULL, progress VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Player');
    }
}
