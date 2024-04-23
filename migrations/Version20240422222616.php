<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422222616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply ADD reply_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0B0AEA55B FOREIGN KEY (reply_id_id) REFERENCES reply (id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E0B0AEA55B ON reply (reply_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0B0AEA55B');
        $this->addSql('DROP INDEX IDX_FDA8C6E0B0AEA55B ON reply');
        $this->addSql('ALTER TABLE reply DROP reply_id_id');
    }
}
