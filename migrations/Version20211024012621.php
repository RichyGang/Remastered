<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024012621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, mother_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C1B78A354D (mother_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_attribute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_attribute_category (category_attribute_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_779C506B6C310D68 (category_attribute_id), INDEX IDX_779C506B12469DE2 (category_id), PRIMARY KEY(category_attribute_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource_attribute (id INT AUTO_INCREMENT NOT NULL, category_attribute_id INT NOT NULL, INDEX IDX_9C3265886C310D68 (category_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource_attribute_ressource (ressource_attribute_id INT NOT NULL, ressource_id INT NOT NULL, INDEX IDX_D2A5210F593A41F6 (ressource_attribute_id), INDEX IDX_D2A5210FFC6CD52A (ressource_id), PRIMARY KEY(ressource_attribute_id, ressource_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B78A354D FOREIGN KEY (mother_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_attribute_category ADD CONSTRAINT FK_779C506B6C310D68 FOREIGN KEY (category_attribute_id) REFERENCES category_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_attribute_category ADD CONSTRAINT FK_779C506B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource_attribute ADD CONSTRAINT FK_9C3265886C310D68 FOREIGN KEY (category_attribute_id) REFERENCES category_attribute (id)');
        $this->addSql('ALTER TABLE ressource_attribute_ressource ADD CONSTRAINT FK_D2A5210F593A41F6 FOREIGN KEY (ressource_attribute_id) REFERENCES ressource_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource_attribute_ressource ADD CONSTRAINT FK_D2A5210FFC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B78A354D');
        $this->addSql('ALTER TABLE category_attribute_category DROP FOREIGN KEY FK_779C506B12469DE2');
        $this->addSql('ALTER TABLE category_attribute_category DROP FOREIGN KEY FK_779C506B6C310D68');
        $this->addSql('ALTER TABLE ressource_attribute DROP FOREIGN KEY FK_9C3265886C310D68');
        $this->addSql('ALTER TABLE ressource_attribute_ressource DROP FOREIGN KEY FK_D2A5210FFC6CD52A');
        $this->addSql('ALTER TABLE ressource_attribute_ressource DROP FOREIGN KEY FK_D2A5210F593A41F6');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_attribute');
        $this->addSql('DROP TABLE category_attribute_category');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE ressource_attribute');
        $this->addSql('DROP TABLE ressource_attribute_ressource');
    }
}
