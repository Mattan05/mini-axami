<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203135821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licensekeys ADD customers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE licensekeys ADD CONSTRAINT FK_7379A20DC3568B40 FOREIGN KEY (customers_id) REFERENCES customers (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7379A20DC3568B40 ON licensekeys (customers_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licensekeys DROP FOREIGN KEY FK_7379A20DC3568B40');
        $this->addSql('DROP INDEX UNIQ_7379A20DC3568B40 ON licensekeys');
        $this->addSql('ALTER TABLE licensekeys DROP customers_id');
    }
}
