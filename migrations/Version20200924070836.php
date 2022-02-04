<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20200924070836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Feld kunde.kunde_anrede ist jetzt varchar';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table kunde
modify column kunde_anrede varchar(31) not null default ''
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table kunde
modify column kunde_anrede enum('','Herr','Frau','Dr.') not null default '',
SQL
        );
    }
}
