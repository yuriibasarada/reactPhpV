<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106082938 extends AbstractMigration
{

    public function up(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE products ADD COLUMN image VARCHAR(255)'
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE products DROP COLUMN image'
        );
    }
}
