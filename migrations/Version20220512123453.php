<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512123453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE direct_message DROP FOREIGN KEY FK_1416AF93B03A8386');
        $this->addSql('DROP INDEX IDX_1416AF93B03A8386 ON direct_message');
        $this->addSql('ALTER TABLE direct_message CHANGE created_by_id create_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE direct_message ADD CONSTRAINT FK_1416AF939E085865 FOREIGN KEY (create_by_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_1416AF939E085865 ON direct_message (create_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE direct_message DROP FOREIGN KEY FK_1416AF939E085865');
        $this->addSql('DROP INDEX IDX_1416AF939E085865 ON direct_message');
        $this->addSql('ALTER TABLE direct_message CHANGE create_by_id created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE direct_message ADD CONSTRAINT FK_1416AF93B03A8386 FOREIGN KEY (created_by_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_1416AF93B03A8386 ON direct_message (created_by_id)');
    }
}
