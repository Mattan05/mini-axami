<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224081425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unit_tasks DROP FOREIGN KEY FK_DB6A1B9CB03A8386');
        $this->addSql('DROP INDEX IDX_DB6A1B9CB03A8386 ON unit_tasks');
        $this->addSql('ALTER TABLE unit_tasks ADD created_by_customer_id INT DEFAULT NULL, CHANGE created_by_id created_by_worker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unit_tasks ADD CONSTRAINT FK_DB6A1B9C3125BF5B FOREIGN KEY (created_by_worker_id) REFERENCES workers (id)');
        $this->addSql('ALTER TABLE unit_tasks ADD CONSTRAINT FK_DB6A1B9C33AC7893 FOREIGN KEY (created_by_customer_id) REFERENCES customers (id)');
        $this->addSql('CREATE INDEX IDX_DB6A1B9C3125BF5B ON unit_tasks (created_by_worker_id)');
        $this->addSql('CREATE INDEX IDX_DB6A1B9C33AC7893 ON unit_tasks (created_by_customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unit_tasks DROP FOREIGN KEY FK_DB6A1B9C3125BF5B');
        $this->addSql('ALTER TABLE unit_tasks DROP FOREIGN KEY FK_DB6A1B9C33AC7893');
        $this->addSql('DROP INDEX IDX_DB6A1B9C3125BF5B ON unit_tasks');
        $this->addSql('DROP INDEX IDX_DB6A1B9C33AC7893 ON unit_tasks');
        $this->addSql('ALTER TABLE unit_tasks ADD created_by_id INT DEFAULT NULL, DROP created_by_worker_id, DROP created_by_customer_id');
        $this->addSql('ALTER TABLE unit_tasks ADD CONSTRAINT FK_DB6A1B9CB03A8386 FOREIGN KEY (created_by_id) REFERENCES workers (id)');
        $this->addSql('CREATE INDEX IDX_DB6A1B9CB03A8386 ON unit_tasks (created_by_id)');
    }
}
