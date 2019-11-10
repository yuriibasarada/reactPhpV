<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191030101310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
       $sql = <<<SQL
CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  product_id INT UNSIGNED not null,
  quantity INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (product_id) REFERENCES products (id)
)
SQL;

       $this->addSql($sql);

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE orders');
    }
}
