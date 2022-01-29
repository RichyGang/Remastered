<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211030231529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B78A354D FOREIGN KEY (mother_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_attribute ADD format VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_attribute ADD CONSTRAINT FK_3D1A3DCBF6859C8C FOREIGN KEY (unity_id) REFERENCES unity (id)');
        $this->addSql('ALTER TABLE category_attribute_category ADD CONSTRAINT FK_779C506B6C310D68 FOREIGN KEY (category_attribute_id) REFERENCES category_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_attribute_category ADD CONSTRAINT FK_779C506B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B78A354D');
        $this->addSql('ALTER TABLE category_attribute DROP FOREIGN KEY FK_3D1A3DCBF6859C8C');
        $this->addSql('ALTER TABLE category_attribute DROP format');
        $this->addSql('ALTER TABLE category_attribute_category DROP FOREIGN KEY FK_779C506B6C310D68');
        $this->addSql('ALTER TABLE category_attribute_category DROP FOREIGN KEY FK_779C506B12469DE2');
    }
}
