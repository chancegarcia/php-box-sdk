<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/22/15
 * Time: 2:24 PM
 * @package     Box
 * @subpackage  Box_Storage
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2016 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Box\Storage\Token\Pdo;

use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Dto\TokenStorageContext;
use Box\Exception\TokenStorageException;
use Box\Storage\Token\Pdo\TokenStorageInterface;
use PDO;
use PDOException;

/**
 * Class TokenStorage
 * @package Box\Storage\Pdo
 */
class TokenStorage implements TokenStorageInterface
{
    protected ?string $dsn = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected array $options = [];
    protected ?PDO $pdo = null;

    protected string $tokenTableName = 'box_token';

    /**
     * map for persistence
     * @var array map contains the database column as the key and the token object getter method as the value.
     */
    protected array $tokenMap = [
        'access_token' => 'getAccessToken',
        'refresh_token' => 'getRefreshToken',
        'grant_type' => 'getGrantType',
        'expires_in' => 'getExpiresIn',
        'token_type' => 'getTokenType',
    ];

    /**
     * construct with pdo or connection arguments
     *
     * @param string|null $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array $options
     * @param PDO|null $pdo
     */
    public function __construct(
        ?string $dsn = null,
        ?string $username = null,
        ?string $password = null,
        array $options = [],
        ?PDO $pdo = null
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        $this->pdo = $pdo;
    }

    /**
     * @return string|null
     */
    public function getDsn(): ?string
    {
        return $this->dsn;
    }

    /**
     * @param string|null $dsn
     */
    public function setDsn(?string $dsn = null): void
    {
        $this->dsn = $dsn;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username = null): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password = null): void
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     */
    public function setOptions(?array $options = null): void
    {
        $this->options = $options ?? [];
    }

    /**
     * @return PDO|null
     */
    public function getPdo(): ?PDO
    {
        if (null === $this->pdo && null !== $this->dsn) {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->pdo;
    }

    /**
     * @param PDO|null $pdo
     */
    public function setPdo(?PDO $pdo = null): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @return string
     */
    public function getTokenTableName(): string
    {
        return $this->tokenTableName;
    }

    /**
     * @param string|null $tokenTableName
     */
    public function setTokenTableName(?string $tokenTableName = null): void
    {
        if (null !== $tokenTableName) {
            $this->tokenTableName = $tokenTableName;
        }
    }

    /**
     * @return array
     */
    public function getTokenMap(): array
    {
        return $this->tokenMap;
    }

    /**
     * @param array|null $tokenMap
     */
    public function setTokenMap(?array $tokenMap = null): void
    {
        if (null !== $tokenMap) {
            $this->tokenMap = $tokenMap;
        }
    }

    /**
     * {@inheritdoc}
     * @throws TokenStorageException
     */
    public function storeToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $this->updateToken($token, $context);
    }

    /**
     * {@inheritdoc}
     * @throws TokenStorageException
     */
    public function updateToken(TokenInterface $token, TokenStorageContext $context): void
    {
        $pdo = $this->getPdo();
        if (null === $pdo) {
            $exception = new TokenStorageException("PDO connection not available.");
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }

        $table = $this->getTokenTableName();
        $map = $this->getTokenMap();

        $columns = ['user_id', 'enterprise_id', 'client_id'];
        $values = [
            $context->getUserId(),
            $context->getEnterpriseId(),
            $context->getClientId(),
        ];

        foreach ($map as $column => $method) {
            $columns[] = $column;
            $values[] = $token->$method();
        }

        $placeholders = array_fill(0, count($columns), '?');

        // Use REPLACE INTO for SQLite/MySQL to handle one-active-token-per-context
        // For general portability, we'll try to use a pattern that works or fallback to manual delete+insert if needed.
        // But the requirement says "Work with SQLite for tests" and "portable enough".
        // A common portable way is DELETE then INSERT, but REPLACE is very clean for this "one-active-token" requirement.
        // Let's use a manual DELETE + INSERT to be more portable across standard SQL if REPLACE isn't supported.

        try {
            $pdo->beginTransaction();

            $deleteSql = sprintf(
                "DELETE FROM %s WHERE %s AND %s AND %s",
                $table,
                $this->getNullableColumnSql('user_id'),
                $this->getNullableColumnSql('enterprise_id'),
                $this->getNullableColumnSql('client_id')
            );
            $deleteStmt = $pdo->prepare($deleteSql);
            $deleteStmt->execute([
                $context->getUserId(),
                $context->getUserId(),
                $context->getUserId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getClientId(),
                $context->getClientId(),
                $context->getClientId(),
            ]);

            $insertSql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute($values);

            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $exception = new TokenStorageException("Failed to store token: " . $e->getMessage(), 0, $e);
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     * @throws TokenStorageException
     */
    public function retrieveToken(TokenStorageContext $context): ?TokenInterface
    {
        $pdo = $this->getPdo();
        if (null === $pdo) {
            $exception = new TokenStorageException("PDO connection not available.");
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }

        $table = $this->getTokenTableName();
        $map = $this->getTokenMap();
        $columns = array_keys($map);

        $sql = sprintf(
            "SELECT %s FROM %s WHERE %s AND %s AND %s LIMIT 1",
            implode(', ', $columns),
            $table,
            $this->getNullableColumnSql('user_id'),
            $this->getNullableColumnSql('enterprise_id'),
            $this->getNullableColumnSql('client_id')
        );

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $context->getUserId(),
                $context->getUserId(),
                $context->getUserId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getClientId(),
                $context->getClientId(),
                $context->getClientId(),
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }

            // Hydrate token. Token constructor accepts array.
            // We need to map DB columns back to what Token expects in its array (which matches $tokenMap keys usually)
            return new Token($row);
        } catch (PDOException $e) {
            $exception = new TokenStorageException("Failed to retrieve token: " . $e->getMessage(), 0, $e);
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     * @throws TokenStorageException
     */
    public function removeToken(TokenStorageContext $context): void
    {
        $pdo = $this->getPdo();
        if (null === $pdo) {
            $exception = new TokenStorageException("PDO connection not available.");
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }

        $table = $this->getTokenTableName();

        $sql = sprintf(
            "DELETE FROM %s WHERE %s AND %s AND %s",
            $table,
            $this->getNullableColumnSql('user_id'),
            $this->getNullableColumnSql('enterprise_id'),
            $this->getNullableColumnSql('client_id')
        );

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $context->getUserId(),
                $context->getUserId(),
                $context->getUserId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getEnterpriseId(),
                $context->getClientId(),
                $context->getClientId(),
                $context->getClientId(),
            ]);
        } catch (PDOException $e) {
            $exception = new TokenStorageException("Failed to remove token: " . $e->getMessage(), 0, $e);
            $exception->setTokenStorage($this);
            $exception->setTokenStorageContext($context);
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     * @throws TokenStorageException
     */
    public function clear(): void
    {
        $pdo = $this->getPdo();
        if (null === $pdo) {
            $exception = new TokenStorageException("PDO connection not available.");
            $exception->setTokenStorage($this);
            throw $exception;
        }

        $table = $this->getTokenTableName();
        $sql = sprintf("DELETE FROM %s", $table);

        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            $exception = new TokenStorageException("Failed to clear tokens: " . $e->getMessage(), 0, $e);
            $exception->setTokenStorage($this);
            throw $exception;
        }
    }

    /**
     * Helper for nullable column comparison in WHERE clause.
     *
     * @param string $column
     * @return string
     */
    protected function getNullableColumnSql(string $column): string
    {
        // Standard SQL way to handle NULL = NULL in comparison is IS NULL.
        // Since we use parameters, we can't easily swap = ? and IS NULL.
        // A common trick is: (column = ? OR (column IS NULL AND ? IS NULL))
        // SQLite and others support this.
        return sprintf("(%s = ? OR (%s IS NULL AND (? IS NULL OR ? = '')))", $column, $column);
    }
}
