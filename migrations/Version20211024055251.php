<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024055251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proposal_user (proposal_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_ADF2C332F4792058 (proposal_id), INDEX IDX_ADF2C332A76ED395 (user_id), PRIMARY KEY(proposal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proposal_user ADD CONSTRAINT FK_ADF2C332F4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proposal_user ADD CONSTRAINT FK_ADF2C332A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE proposal_user');
    }
}
