<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024054901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposal ADD author_id INT NOT NULL, ADD ressource_id INT NOT NULL');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('CREATE INDEX IDX_BFE59472F675F31B ON proposal (author_id)');
        $this->addSql('CREATE INDEX IDX_BFE59472FC6CD52A ON proposal (ressource_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE59472F675F31B');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE59472FC6CD52A');
        $this->addSql('DROP INDEX IDX_BFE59472F675F31B ON proposal');
        $this->addSql('DROP INDEX IDX_BFE59472FC6CD52A ON proposal');
        $this->addSql('ALTER TABLE proposal DROP author_id, DROP ressource_id');
    }
}
