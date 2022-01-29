<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024015348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_attribute ADD unity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category_attribute ADD CONSTRAINT FK_3D1A3DCBF6859C8C FOREIGN KEY (unity_id) REFERENCES unity (id)');
        $this->addSql('CREATE INDEX IDX_3D1A3DCBF6859C8C ON category_attribute (unity_id)');
        $this->addSql('ALTER TABLE ressource_attribute ADD unity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ressource_attribute ADD CONSTRAINT FK_9C326588F6859C8C FOREIGN KEY (unity_id) REFERENCES unity (id)');
        $this->addSql('CREATE INDEX IDX_9C326588F6859C8C ON ressource_attribute (unity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_attribute DROP FOREIGN KEY FK_3D1A3DCBF6859C8C');
        $this->addSql('DROP INDEX IDX_3D1A3DCBF6859C8C ON category_attribute');
        $this->addSql('ALTER TABLE category_attribute DROP unity_id');
        $this->addSql('ALTER TABLE ressource_attribute DROP FOREIGN KEY FK_9C326588F6859C8C');
        $this->addSql('DROP INDEX IDX_9C326588F6859C8C ON ressource_attribute');
        $this->addSql('ALTER TABLE ressource_attribute DROP unity_id');
    }
}
