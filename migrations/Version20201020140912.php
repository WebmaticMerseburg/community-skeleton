<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20201020140912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'zusätzl. felder für Kunden';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table kunde
add column if not exists kunde_zusatz varchar(127) not null default '' after kunde_strasse,
add column if not exists kunde_email varchar(63) not null default '' after kunde_land,
add column if not exists kunde_web varchar(127) not null default '' after kunde_email,
add column if not exists kunde_telefon varchar(63) not null default '' after kunde_web,
add column if not exists kunde_telefon2 varchar(63) not null default '' after kunde_telefon,
add column if not exists kunde_telefon3 varchar(63) not null default '' after kunde_telefon2,
add column if not exists kunde_fax varchar(63) not null default '' after kunde_telefon3
SQL
        );
        $this->addSql(<<<SQL
create table if not exists kunde_ansprechpartner (
    kunde_ansprechpartner_id int unsigned not null auto_increment primary key,
    kunde_id int unsigned not null,
    kunde_ansprechpartner_lexware tinyint(1) not null default 0,
    kunde_ansprechpartner_name varchar(127) not null default '',
    kunde_ansprechpartner_filiale varchar(63) not null default '',
    kunde_ansprechpartner_telefon varchar(63) not null default '',
    kunde_ansprechpartner_email varchar(63) not null default '',
    foreign key kunde_ansprechpartner_kunde (kunde_id) references kunde (kunde_id) on update cascade on delete cascade
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('drop table if exists kunde_ansprechpartner');
        $this->addSql(<<<SQL
alter table kunde
drop column if exists kunde_zusatz,
drop column if exists kunde_email
drop column if exists kunde_web,
drop column if exists kunde_telefon,
drop column if exists kunde_telefon2,
drop column if exists kunde_telefon3,
drop column if exists kunde_fax
SQL
        );
    }
}
