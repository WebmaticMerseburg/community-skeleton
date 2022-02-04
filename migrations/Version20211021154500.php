<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20211021154500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'created/modified für Kunden';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table kunde
add column if not exists created datetime not null default '1970-01-01 00:00:00',
add column if not exists modified datetime default null
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table kunde
drop column if exists created,
drop column if exists modified
SQL
        );
    }
}
