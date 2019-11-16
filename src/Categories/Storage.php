<?php


namespace App\Categories;


use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

final class Storage
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(int $uid, string $name, float $limit): PromiseInterface
    {
        return $this->connection->query(
            'INSERT INTO categories (user_id, name, `limit`) 
                  VALUES (?, ?, ?)', [$uid, $name, $limit]
            )
            ->then(
                function (QueryResult $queryResult) use ($uid, $name, $limit){
                    return new Category($queryResult->insertId, $uid, $name, $limit);
                }
            );
    }

    public function getByUserId(int $id): PromiseInterface
    {
        return $this->connection
        ->query(
            'SELECT id, user_id, name, `limit` FROM categories WHERE user_id = ?', [$id]
        )
        ->then(
            function(QueryResult $queryResult) {
                if(empty($queryResult->resultRows)) {
                    throw new CategoryNotFound();
                }
                return $this->mapCategory($queryResult->resultRows[0]);
            }
        );
    }


    public function getAll(int $uid): PromiseInterface
    {
        return  $this->connection
            ->query(
                'SELECT id, name, `limit`, user_id FROM categories WHERE user_id = ?', [$uid]
            )
            ->then(function (QueryResult $result) {
                return array_map(function (array $row) {
                    return $this->mapCategory($row);
                }, $result->resultRows);
            });
    }

    public function update(int $id, string $newName, float $newLimit): PromiseInterface
    {
        return $this->getById($id)->then(function (Category $category) use ($newName, $newLimit) {
            return $this->connection
                ->query(
                    'UPDATE categories SET name =?, `limit` = ? WHERE id = ?',
                    [$newName, $newLimit, $category->id]
                );
        });
    }

    public function getById(int $id): PromiseInterface
    {
        return  $this->connection
            ->query(
                'SELECT id, name, `limit`, user_id FROM categories WHERE id = ?', [$id]
            )
            ->then(function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new CategoryNotFound();
                }
                return $this->mapCategory($result->resultRows[0]);
            });
    }

    private function mapCategory($row): Category
    {
        return new Category(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['name'],
            (float) $row['limit']
        );
    }


}