<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20190411101143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL

create table if not exists `kunde` (
    `kunde_id` int(10) unsigned not null primary key,
    `kunde_matchcode` varchar(63) not null default '',
    `kunde_anrede` enum('','Herr','Frau','Dr.') not null default '',
    `kunde_name` varchar(64) not null default '',
    `kunde_vorname` varchar(31) not null default '',
    `kunde_firma` varchar(63) not null default '',
    `kunde_strasse` varchar(127) not null default '',
    `kunde_plz` varchar(15) not null default '',
    `kunde_ort` varchar(63) not null default '',
    `kunde_land` varchar(31) not null default ''
)
SQL
        );
        $this->addSql(<<<SQL

alter table `user`
add column if not exists `kunde_id` int unsigned default null after `password`,
add foreign key if not exists `user_ibfk_3` (`kunde_id`) references `kunde` (`kunde_id`) on update cascade
SQL
        );
        $this->addSql(<<<SQL

create table if not exists `favourite` (
    `kunde_id` int unsigned not null,
    `user_id` int(10) unsigned not null,
    primary key (`kunde_id`,`user_id`),
    key `user_id` (`user_id`),
    foreign key `favourite_ibfk_1` (`kunde_id`) references `kunde` (`kunde_id`) on delete cascade on update cascade,
    foreign key `favourite_ibfk_2` (`user_id`) references `user` (`user_id`) on delete cascade on update cascade
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table if exists `favourite`');
        $this->addSql(<<<SQL

alter table `user`
drop foreign key if exists `user_ibfk_3`,
drop column if exists `kunde_id`
SQL
        );
        $this->addSql('drop table if exists `kunde`');
    }
}
