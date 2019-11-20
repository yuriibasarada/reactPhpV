<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112070601 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
CREATE TABLE records (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    category_id int unsigned not null,
    amount int unsigned not null,
    description TEXT,
    type_id int unsigned not null,
    date datetime default NOW(),
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (type_id) REFERENCES records_type (id)
)
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE records');
    }
}
