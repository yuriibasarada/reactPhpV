<?php


namespace App\Records;


use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class Storage
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(int $categoryId, int $amount, string $description, int $typeId, string $date): PromiseInterface
    {
        return $this->getTypeById($typeId)->then(
            function (array $type) use ($categoryId, $amount, $description, $date) {
                return $this->connection->query(
                    'INSERT INTO records (category_id, amount, description, type_id, date) 
                         VALUES (?, ?, ?, ?, ?)',
                    [$categoryId, $amount, $description, $type['id'], $date]
                )->then(
                    function (QueryResult $result) use ($categoryId, $amount, $description, $type, $date) {
                        return new Record($result->insertId, $categoryId, $amount, $description, $type['id'], $date);
                    }
                );
            }
        );
    }

    public function getTypeById($id): PromiseInterface
    {
        return $this->connection->query(
            'SELECT id, name FROM records_type WHERE id = ?', [$id]
        )->then(
            function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new TypeNotFound();
                }
                return $result->resultRows[0];
            }
        );
    }

    public function getAll(): PromiseInterface
    {
        return $this->connection->query(
            'SELECT id, category_id, amount, description, type_id, date FROM records'
        )->then(function (QueryResult $result) {
            return array_map(function ($row) {
                return $this->mapRecord($row);
            }, $result->resultRows);
        });
    }

    public function getById(int $id): PromiseInterface
    {
        return $this->connection->query(
            'SELECT id, category_id, amount, description, type_id, date FROM records WHERE id = ?', [$id]
        )->then(function (QueryResult $result) {
            if(empty($result->resultRows)) {
                throw new RecordNotFound();
            }
            return $this->mapRecord($result->resultRows[0]);
        });
    }
    private function mapRecord(array $record)
    {
        return new Record(
            (int)$record['id'],
            (int)$record['category_id'],
            (int)$record['amount'],
            (string)$record['description'],
            (int)$record['type_id'],
            (string)$record['date']
        );
    }
}