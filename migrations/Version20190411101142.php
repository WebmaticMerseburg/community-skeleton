<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20190411101142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL

create table if not exists `user` (
    `user_id` int unsigned not null primary key,
    `username` varchar(180) not null,
    `email` varchar(180) not null,
    `enabled` tinyint(1) unsigned not null,
    `password` varchar(255) not null,
    `created` datetime default null,
    `created_by` int unsigned default null,
    `modified` datetime default null,
    `modified_by` int unsigned default null,
    `last_login` datetime default null,
    `last_activity` datetime default null,
    `confirmation_token` varchar(180) default null,
    `password_requested_at` datetime default null,
    `roles` longtext not null comment '(DC2Type:array)',
    unique key `username` (`username`),
    unique key `email` (`email`),
    unique key `confirmation_token` (`confirmation_token`),
    key `created_by` (`created_by`),
    key `modified_by` (`modified_by`),
    foreign key `user_ibfk_1` (`created_by`) references `user` (`user_id`) on delete set null on update cascade,
    foreign key `user_ibfk_2` (`modified_by`) references `user` (`user_id`) on delete set null on update cascade
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table if exists `user`');
    }
}
