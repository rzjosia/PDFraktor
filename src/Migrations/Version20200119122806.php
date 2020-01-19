<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200119122806 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pdf_url (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expire_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pdf_document ADD pdf_url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pdf_document ADD CONSTRAINT FK_73CB0DAEE7587660 FOREIGN KEY (pdf_url_id) REFERENCES pdf_url (id)');
        $this->addSql('CREATE INDEX IDX_73CB0DAEE7587660 ON pdf_document (pdf_url_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pdf_document DROP FOREIGN KEY FK_73CB0DAEE7587660');
        $this->addSql('DROP TABLE pdf_url');
        $this->addSql('DROP INDEX IDX_73CB0DAEE7587660 ON pdf_document');
        $this->addSql('ALTER TABLE pdf_document DROP pdf_url_id');
    }
}
