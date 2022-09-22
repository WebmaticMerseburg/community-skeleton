<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217144326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'modify table user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table `user`
add unique key if not exists `username` (`username`),
add unique key if not exists `email` (`email`),
drop constraint if exists `username_canonical`,
drop constraint if exists `email_canonical`,
drop column if exists `username_canonical`,
drop column if exists `email_canonical`
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
alter table `user`
add column if not exists `username_canonical` varchar(180) not null default '' after `username`,
add column if not exists `email_canonical` varchar(180) not null default '' after `email`
SQL
        );
        $this->addSql(<<<SQL
update `user`
set `username_canonical` = `username`, `email_canonical` = `email`
SQL
        );
        $this->addSql(<<<SQL
alter table `user`
add unique key if not exists `username_canonical`(`username_canonical`),
add unique key if not exists `email_canonical`(`email_canonical`),
drop constraint if exists `username`,
drop constraint if exists `email`
SQL
        );
    }
}
