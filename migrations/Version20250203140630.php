<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203140630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E2177FA3B35');
        $this->addSql('DROP INDEX UNIQ_62534E2177FA3B35 ON customers');
        $this->addSql('ALTER TABLE customers DROP license_key_id');
        $this->addSql('ALTER TABLE licensekeys DROP FOREIGN KEY FK_7379A20DC3568B40');
        $this->addSql('ALTER TABLE licensekeys ADD CONSTRAINT FK_7379A20DC3568B40 FOREIGN KEY (customers_id) REFERENCES customers (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers ADD license_key_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E2177FA3B35 FOREIGN KEY (license_key_id) REFERENCES licensekeys (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E2177FA3B35 ON customers (license_key_id)');
        $this->addSql('ALTER TABLE licensekeys DROP FOREIGN KEY FK_7379A20DC3568B40');
        $this->addSql('ALTER TABLE licensekeys ADD CONSTRAINT FK_7379A20DC3568B40 FOREIGN KEY (customers_id) REFERENCES customers (id)');
    }
}
