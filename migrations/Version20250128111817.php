<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128111817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, license_key_id INT DEFAULT NULL, identification_number VARCHAR(20) NOT NULL, customer_email VARCHAR(255) NOT NULL, customer_type VARCHAR(25) NOT NULL, license_valid TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_62534E2177FA3B35 (license_key_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licensekeys (id INT AUTO_INCREMENT NOT NULL, license_key VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, valid_until DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_tasks (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, status VARCHAR(100) NOT NULL, category VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, task_title VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_DB6A1B9CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_tasks_units (unit_tasks_id INT NOT NULL, units_id INT NOT NULL, INDEX IDX_CB5B25C89619C7A3 (unit_tasks_id), INDEX IDX_CB5B25C899387CE8 (units_id), PRIMARY KEY(unit_tasks_id, units_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assigned_unit_tasks_workers (unit_tasks_id INT NOT NULL, workers_id INT NOT NULL, INDEX IDX_1F6C61399619C7A3 (unit_tasks_id), INDEX IDX_1F6C613928A00806 (workers_id), PRIMARY KEY(unit_tasks_id, workers_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_tasks_workers (unit_tasks_id INT NOT NULL, workers_id INT NOT NULL, INDEX IDX_86D997439619C7A3 (unit_tasks_id), INDEX IDX_86D9974328A00806 (workers_id), PRIMARY KEY(unit_tasks_id, workers_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE units (id INT AUTO_INCREMENT NOT NULL, customer_id_id INT NOT NULL, description LONGTEXT NOT NULL, unit_name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, status VARCHAR(50) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_E9B07449B171EB6C (customer_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workers (id INT AUTO_INCREMENT NOT NULL, worker_email VARCHAR(255) NOT NULL, phone_number VARCHAR(25) NOT NULL, full_name VARCHAR(255) NOT NULL, employment_type VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workers_customers (workers_id INT NOT NULL, customers_id INT NOT NULL, INDEX IDX_2775B35F28A00806 (workers_id), INDEX IDX_2775B35FC3568B40 (customers_id), PRIMARY KEY(workers_id, customers_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workers_units (workers_id INT NOT NULL, units_id INT NOT NULL, INDEX IDX_EA377A5428A00806 (workers_id), INDEX IDX_EA377A5499387CE8 (units_id), PRIMARY KEY(workers_id, units_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E2177FA3B35 FOREIGN KEY (license_key_id) REFERENCES licensekeys (id)');
        $this->addSql('ALTER TABLE unit_tasks ADD CONSTRAINT FK_DB6A1B9CB03A8386 FOREIGN KEY (created_by_id) REFERENCES workers (id)');
        $this->addSql('ALTER TABLE unit_tasks_units ADD CONSTRAINT FK_CB5B25C89619C7A3 FOREIGN KEY (unit_tasks_id) REFERENCES unit_tasks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit_tasks_units ADD CONSTRAINT FK_CB5B25C899387CE8 FOREIGN KEY (units_id) REFERENCES units (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assigned_unit_tasks_workers ADD CONSTRAINT FK_1F6C61399619C7A3 FOREIGN KEY (unit_tasks_id) REFERENCES unit_tasks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assigned_unit_tasks_workers ADD CONSTRAINT FK_1F6C613928A00806 FOREIGN KEY (workers_id) REFERENCES workers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit_tasks_workers ADD CONSTRAINT FK_86D997439619C7A3 FOREIGN KEY (unit_tasks_id) REFERENCES unit_tasks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unit_tasks_workers ADD CONSTRAINT FK_86D9974328A00806 FOREIGN KEY (workers_id) REFERENCES workers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE units ADD CONSTRAINT FK_E9B07449B171EB6C FOREIGN KEY (customer_id_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE workers_customers ADD CONSTRAINT FK_2775B35F28A00806 FOREIGN KEY (workers_id) REFERENCES workers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workers_customers ADD CONSTRAINT FK_2775B35FC3568B40 FOREIGN KEY (customers_id) REFERENCES customers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workers_units ADD CONSTRAINT FK_EA377A5428A00806 FOREIGN KEY (workers_id) REFERENCES workers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workers_units ADD CONSTRAINT FK_EA377A5499387CE8 FOREIGN KEY (units_id) REFERENCES units (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers DROP FOREIGN KEY FK_62534E2177FA3B35');
        $this->addSql('ALTER TABLE unit_tasks DROP FOREIGN KEY FK_DB6A1B9CB03A8386');
        $this->addSql('ALTER TABLE unit_tasks_units DROP FOREIGN KEY FK_CB5B25C89619C7A3');
        $this->addSql('ALTER TABLE unit_tasks_units DROP FOREIGN KEY FK_CB5B25C899387CE8');
        $this->addSql('ALTER TABLE assigned_unit_tasks_workers DROP FOREIGN KEY FK_1F6C61399619C7A3');
        $this->addSql('ALTER TABLE assigned_unit_tasks_workers DROP FOREIGN KEY FK_1F6C613928A00806');
        $this->addSql('ALTER TABLE unit_tasks_workers DROP FOREIGN KEY FK_86D997439619C7A3');
        $this->addSql('ALTER TABLE unit_tasks_workers DROP FOREIGN KEY FK_86D9974328A00806');
        $this->addSql('ALTER TABLE units DROP FOREIGN KEY FK_E9B07449B171EB6C');
        $this->addSql('ALTER TABLE workers_customers DROP FOREIGN KEY FK_2775B35F28A00806');
        $this->addSql('ALTER TABLE workers_customers DROP FOREIGN KEY FK_2775B35FC3568B40');
        $this->addSql('ALTER TABLE workers_units DROP FOREIGN KEY FK_EA377A5428A00806');
        $this->addSql('ALTER TABLE workers_units DROP FOREIGN KEY FK_EA377A5499387CE8');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE licensekeys');
        $this->addSql('DROP TABLE unit_tasks');
        $this->addSql('DROP TABLE unit_tasks_units');
        $this->addSql('DROP TABLE assigned_unit_tasks_workers');
        $this->addSql('DROP TABLE unit_tasks_workers');
        $this->addSql('DROP TABLE units');
        $this->addSql('DROP TABLE workers');
        $this->addSql('DROP TABLE workers_customers');
        $this->addSql('DROP TABLE workers_units');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
