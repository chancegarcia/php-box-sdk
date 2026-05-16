<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 2:55 PM
 *
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
 */

namespace Box\Service;

use Box\Dto\PagedResult;
use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Exception\BoxException;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\TokenInterface;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxApiErrorTrait;
use Box\Mapper\Hydrator;
use OutOfBoundsException;
use RuntimeException;
use BadMethodCallException;
use stdClass;

class Service implements ServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxApiErrorTrait;

    protected ?ConnectionInterface $connection = null;

    protected ?TokenInterface $token = null;

    /**
     * @throws RuntimeException
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        if (!$this->connection instanceof ConnectionInterface) {
            throw new RuntimeException("ConnectionInterface not found");
        }

        $token = $this->getToken();
        $this->connection->setAccessToken($token->getAccessToken());

        return $this->connection;
    }

    public function setConnection(?ConnectionInterface $connection = null): void
    {
        $this->connection = $connection;
    }

    /**
     * @throws RuntimeException
     */
    public function getToken(): TokenInterface
    {
        if (!$this->token instanceof TokenInterface) {
            throw new \RuntimeException('TokenInterface not found');
        }

        return $this->token;
    }

    public function setToken(?TokenInterface $token = null): void
    {
        $this->token = $token;
    }

    /**
     * @param ?BoxResponseInterface $response
     * @param string $returnType 'decoded', 'flat', 'array', or 'original'
     *
     * @throws BoxResponseException
     * @throws BadMethodCallException
     * @throws OutOfBoundsException
     * @return mixed
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, string $returnType = 'decoded'): mixed
    {
        if (!$response instanceof BoxResponseInterface) {
            throw new BadMethodCallException("expecting instance of " . BoxResponseInterface::class . ". received: " . gettype($response));
        }

        if (!$response->isSuccessful()) {
            throw $this->processResponseError($response);
        }

        $flat = $response->json(true);
        $json = $response->getContent();

        if (null === $flat && '' !== (string)$json) {
            $this->error([
                'error' => 'sdk_json_decode',
                'error_description' => 'unable to decode or recursion level too deep',
            ]);
        }

        return match ($returnType) {
            'decoded' => $response->json(false),
            'flat', 'array' => $flat,
            'original' => $json,
            default => throw new OutOfBoundsException($returnType . ' is not a valid result type.'),
        };
    }

    /**
     * @param BoxResponseInterface $response
     *
     * @return BoxResponseException
     */
    protected function processResponseError(BoxResponseInterface $response): BoxResponseException
    {
        $e = new BoxResponseException("Box Response was unsuccessful. ", $response->getStatusCode(), null, $response);

        // Handle Retry-After header
        $delay = $response->getRetryAfter();
        if (null !== $delay) {
            $e->addContext($response->getHeaderLine('Retry-After'), 'retry_after_header');
            $e->addContext($delay, 'retry_after_seconds');
        }

        return $e;
    }

    /**
     * Send a DELETE request and discard the (typically 204) response.
     *
     * @param string $uri
     *
     * @throws BoxResponseException
     */
    protected function sendDeleteToBox(string $uri): void
    {
        $response = $this->getConnection()->delete($uri);
        $this->handleBoxResponse($response, 'flat');
    }

    /**
     * Hydrate a decoded payload into a class.
     *
     * @param class-string<T> $targetClass
     * @param array|stdClass $data
     *
     * @return T
     *
     * @template T of object
     */
    protected function hydrate(string $targetClass, array|stdClass $data): object
    {
        /** @var T */
        return (new Hydrator())->hydrate($targetClass, $data);
    }

    /**
     * Helper to get and hydrate a resource from Box.
     *
     * @param string $uri
     * @param class-string<T> $resourceClass
     *
     * @throws BoxException
     * @return T
     *
     * @template T of object
     */
    protected function getResourceFromBox(string $uri, string $resourceClass): object
    {
        $response = $this->getConnection()->query($uri);
        $data = $this->handleBoxResponse($response, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }

    /**
     * Hydrate a Box paged-collection response into a typed PagedResult.
     *
     * @param array $data Flat response array with 'entries', 'total_count', 'offset', 'limit'
     * @param class-string<T> $entryClass
     *
     * @return PagedResult<T>
     *
     * @template T of object
     */
    protected function hydratePagedResult(array $data, string $entryClass): PagedResult
    {
        $entries = array_map(
            fn(array $entry) => $this->hydrate($entryClass, $entry),
            $data['entries'] ?? [],
        );

        return new PagedResult(
            entries: $entries,
            totalCount: (int) ($data['total_count'] ?? 0),
            offset: (int) ($data['offset'] ?? 0),
            limit: (int) ($data['limit'] ?? 0),
        );
    }

    /**
     * Helper to send an update to Box and hydrate the response.
     *
     * @param string $uri
     * @param array|string $params
     * @param class-string<T> $resourceClass
     *
     * @throws BoxException
     * @throws \JsonException
     * @return T
     *
     * @template T of object
     */
    protected function sendUpdateAndHydrate(string $uri, array|string $params, string $resourceClass): object
    {
        if (!is_string($params)) {
            $params = json_encode($params, JSON_THROW_ON_ERROR);
        }
        $response = $this->getConnection()->put($uri, $params);
        $data = $this->handleBoxResponse($response, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }
}
