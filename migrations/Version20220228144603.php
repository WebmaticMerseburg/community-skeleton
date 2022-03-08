<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220228144603 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
            CREATE TABLE uvdesk.user_kunde_matching (
                kunde_id int(10) unsigned NOT NULL,
                user_id int(11) NOT NULL,
                CONSTRAINT user_kunde_matching_PK PRIMARY KEY (kunde_id,user_id),
                CONSTRAINT user_kunde_matching_FK FOREIGN KEY (kunde_id) REFERENCES uvdesk.kunde(kunde_id) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT user_kunde_matching_FK_1 FOREIGN KEY (user_id) REFERENCES uvdesk.uv_user(id) ON DELETE CASCADE ON UPDATE CASCADE
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_general_ci;
SQL
        );
        $this->addSql(<<<SQL
            CREATE TABLE `kunde_domain` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `kunde_id` int(10) unsigned NOT NULL,
                `domain` varchar(63) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `kunde_domain_UN` (`kunde_id`),
                UNIQUE KEY `kunde_domain_UN_1` (`domain`),
                CONSTRAINT `kunde_domain_FK` FOREIGN KEY (`kunde_id`) REFERENCES `kunde` (`kunde_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB 
              DEFAULT CHARSET=utf8mb4 
              COLLATE=utf8mb4_general_ci;
SQL
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DROP TABLE uvdesk.user_kunde_matching;");
        $this->addSql("DROP TABLE uvdesk.kunde_domain;");
    }
}
