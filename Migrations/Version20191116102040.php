<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191116102040 extends AbstractMigration
{


    public function up(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE users_info ADD COLUMN locale VARCHAR(255) DEFAULT "ru-RU"'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE users_info DROP COLUMN users_info');
    }
}
