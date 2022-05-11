<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324140625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS uvdesk.service_hours (
            id int(10) unsigned auto_increment NOT NULL,
            name varchar(100) NOT NULL,
            CONSTRAINT service_hours_PK PRIMARY KEY (id)
        )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4
        COLLATE=utf8mb4_general_ci;     
SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS uvdesk.timespan (
            id int(10) unsigned auto_increment NOT NULL,
            `begin` TIME NOT NULL,
            `end` TIME NOT NULL,
            on_non_working_days BOOL NULL,
            service_hours_id int(10) unsigned NOT NULL,
            CONSTRAINT timespan_PK PRIMARY KEY (id),
            CONSTRAINT timespan_FK FOREIGN KEY (service_hours_id) REFERENCES uvdesk.service_hours(id) ON DELETE CASCADE ON UPDATE CASCADE
        )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4
        COLLATE=utf8mb4_general_ci;   
SQL);

    }

    public function down(Schema $schema) : void
    {
    	$this->addSql("DROP TABLE uvdesk.timespan;");
        $this->addSql("DROP TABLE uvdesk.service_hours;");

    }
}
