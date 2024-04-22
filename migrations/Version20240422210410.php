<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422210410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E04B89032C ON reply (post_id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E0A76ED395 ON reply (user_id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1BA76ED395 ON topic (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E04B89032C');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0A76ED395');
        $this->addSql('DROP INDEX IDX_FDA8C6E04B89032C ON reply');
        $this->addSql('DROP INDEX IDX_FDA8C6E0A76ED395 ON reply');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BA76ED395');
        $this->addSql('DROP INDEX IDX_9D40DE1BA76ED395 ON topic');
    }
}
