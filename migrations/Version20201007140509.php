<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
* Auto-generated Migration: Please modify to your needs!
*/
final class Version20201007140509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'table dashboard_action_config';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
create table if not exists dashboard_action_config (
    dashboard_action_config_id int unsigned not null auto_increment primary key,
    user_id int unsigned not null,
    dashboard_action_config_key varchar(63) not null,
    dashboard_action_config_params json default null,
    unique(user_id, dashboard_action_config_key),
    foreign key fk_dashboard_action_config_user (user_id) references user (user_id) on update cascade on delete cascade
)
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('drop table if exists dashboard_action_config');
    }
}
