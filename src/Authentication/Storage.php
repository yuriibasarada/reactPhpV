<?php


namespace App\Authentication;


use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

final class Storage
{
    private const START_BILL = 1000;
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(string $email, string $password, ?string $name): PromiseInterface
    {
        return $this->emailIsNotTaken($email)
            ->then(
                function () use ($email, $password){
                    return $this->connection
                        ->query(
                            'INSERT INTO users(email, password) VALUES (?, ?)',
                            [$email, $password]
                        );
                }
            )
            ->then(
                function (QueryResult $command) use ($name) {
                    return $this->connection
                        ->query(
                            'INSERT INTO users_info(user_id, name, bill) VALUES (?, ?, ?)',
                            [$command->insertId, $name, self::START_BILL]
                        );
                }
            );
    }

    private function emailIsNotTaken(string $email): PromiseInterface
    {
        return $this->connection
            ->query('SELECT 1 FROM users WHERE email = ?', [$email])
            ->then(
                function (QueryResult $result) {
                          return empty($result->resultRows) ? resolve() :  reject(new UserAlreadyExists());
                }
            );

    }

    public function findByEmail(string $email): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT id, email, password FROM users WHERE email = ?',
                [$email]
            )
            ->then(function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new UserNotFound();
                }

                $row = $result->resultRows[0];
                return new User(
                    (int) $row['id'],
                    $row['email'],
                    $row['password']
                );
            });
    }

    public function getById(int $id) {
        return $this->connection
            ->query(
                'SELECT id, email, password FROM users WHERE id = ?', [$id]
            )
            ->then(
                function (QueryResult $result) {
                    if (empty($result->resultRows)) {
                        throw new UserNotFound();
                    }
                    return $result->resultRows[0];
                }
            );
    }
    public function getInfoById(int $id): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT id, name, bill, locale FROM users_info WHERE user_id = ?',
                [$id]
            )
            ->then(function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new UserNotFound();
                }

                return $result->resultRows[0];

            });
    }

    public function updateInfo(int $uid, string $name, int $bill, string $locale): PromiseInterface
    {
        return $this->getInfoById($uid)
            ->then( function (array $info) use ($uid, $name, $bill, $locale) {
                return $this->connection->query(
                  'UPDATE users_info SET name = ?, bill = ?, locale = ? WHERE user_id = ?', [$name, $bill, $locale, $uid]);
            });
    }
}