<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024014849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE unity (id INT AUTO_INCREMENT NOT NULL, grandeur_id INT DEFAULT NULL, degree TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(255) NOT NULL, INDEX IDX_9659D572069AD09 (grandeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE unity ADD CONSTRAINT FK_9659D572069AD09 FOREIGN KEY (grandeur_id) REFERENCES unity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unity DROP FOREIGN KEY FK_9659D572069AD09');
        $this->addSql('DROP TABLE unity');
    }
}
