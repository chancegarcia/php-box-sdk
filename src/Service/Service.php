<?php

/**
 * Created by PhpStorm.
 * User: chance
 * Date: 9/18/15
 * Time: 2:55 PM
 * @package     Box
 * @subpackage  Box_Model
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

namespace Box\Service;

use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Exception\BoxException;
use Box\Connection\ConnectionInterface;
use Box\Connection\Token\Token;
use Box\Connection\Token\TokenInterface;
use Box\Logger\LoggerAwareInterface;
use Box\Trait\LoggerAwareTrait;
use Box\Trait\BoxLoggerTrait;
use Box\Mapper\Hydrator;
use OutOfBoundsException;
use RuntimeException;
use InvalidArgumentException;
use BadMethodCallException;
use stdClass;

class Service implements ServiceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use BoxLoggerTrait;

    /**
     * @var ConnectionInterface|null
     */
    protected $connection;

    /**
     * @var TokenInterface|null
     */
    protected $token;

    /**
     * @var array
     */
    private $allowedReturnTypes = [
        'decoded',
        'original',
        'flat',
        'array',
    ];

    /**
     * {@inheritdoc}
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        if (!$this->connection instanceof ConnectionInterface) {
            throw new RuntimeException("ConnectionInterface not found");
        }

        $token = $this->getToken();
        $this->connection->setAccessToken($token->getAccessToken());

        return $this->connection;
    }

    /**
     * @param ConnectionInterface|null $connection
     * @return void
     */
    public function setConnection($connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        if (!$this->token instanceof TokenInterface) {
            throw new \RuntimeException('TokenInterface not found');
        }

        return $this->token;
    }

    /**
     * @param Token|TokenInterface $token
     * @return void
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }

    /**
     * @param string $type
     * @return void
     */
    protected function validateReturnType($type = null)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('string type expected');
        }

        if ('array' === $type) {
            return;
        }

        if (!in_array($type, $this->allowedReturnTypes)) {
            $validTypes = implode(", ", $this->allowedReturnTypes);
            throw new OutOfBoundsException($type . " is not a valid result type. valid types: " . $validTypes);
        }
    }


    /**
     * @param ?BoxResponseInterface $response
     * @param string $returnType
     * @return mixed
     * @throws BoxResponseException
     */
    public function handleBoxResponse(?BoxResponseInterface $response = null, $returnType = 'decoded')
    {
        if (!$response instanceof BoxResponseInterface) {
            throw new BadMethodCallException("expecting instance of " . BoxResponseInterface::class . ". received: " . gettype($response));
        }

        // here is where we decide to throw exceptions based on response
        if (!$response->isSuccessful()) {
            throw $this->processResponseError($response);
        }

        $data = $this->handleResponseContent($returnType, $response);

        return $data;
    }

    /**
     * @param BoxResponseInterface $response
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
     * @throws BoxResponseException
     */
    protected function sendDeleteToBox(string $uri): void
    {
        $response = $this->getConnection()->delete($uri);
        $this->handleBoxResponse($response, 'flat');
    }

    /**
     * @param string $returnType
     * @param BoxResponseInterface|string $response
     * @return mixed
     * @throws BoxException
     * @deprecated v0.11.0 use handleBoxResponse or process modern responses directly
     */
    protected function handleResponseContent($returnType, $response): mixed
    {
        $this->validateReturnType($returnType);

        if ($response instanceof BoxResponseInterface) {
            $json = $response->getContent();
            $decoded = $response->json(false);
            $flat = $response->json(true);
        } else {
            $json = $response;
            $decoded = json_decode((string)$json);
            $flat = json_decode((string)$json, true);
        }

        $data = match ($returnType) {
            'decoded' => $decoded,
            'flat', 'array' => $flat,
            'original' => $json,
            default => throw new OutOfBoundsException($returnType . " is not a valid result type."),
        };

        if (null === $flat && '' !== (string)$json) {
            $this->error([
                'error' => "sdk_json_decode",
                'error_description' => "unable to decode or recursion level too deep",
            ]);
        }

        if (is_array($flat)) {
            if (array_key_exists('error', $flat)) {
                $this->error($flat);
            }

            if (array_key_exists('type', $flat) && 'error' === $flat['type']) {
                $errorData = [
                    'error' => "sdk_unknown",
                    'error_description' => "sdk_unknown",
                    'result_data' => $json,
                ];

                if (array_key_exists('code', $flat)) {
                    $errorData['code'] = $flat['code'];
                }

                $this->error($errorData);
            }
        }

        return $data;
    }

    /**
     * Hydrate a decoded payload into a class.
     *
     * @template T of object
     * @param class-string<T> $targetClass
     * @param array|stdClass $data
     * @return T
     */
    protected function hydrate(string $targetClass, array|stdClass $data): object
    {
        /** @var T */
        return (new Hydrator())->hydrate($targetClass, $data);
    }

    /**
     * Helper to get and hydrate a resource from Box.
     *
     * @template T of object
     * @param string $uri
     * @param class-string<T> $resourceClass
     * @return T
     * @throws BoxException
     */
    protected function getResourceFromBox(string $uri, string $resourceClass): object
    {
        $response = $this->getConnection()->query($uri);
        $data = $this->handleBoxResponse($response, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }

    /**
     * Helper to send an update to Box and hydrate the response.
     *
     * @template T of object
     * @param string $uri
     * @param array|string $params
     * @param class-string<T> $resourceClass
     * @return T
     * @throws BoxException
     */
    protected function sendUpdateAndHydrate(string $uri, array|string $params, string $resourceClass): object
    {
        if (!is_string($params)) {
            $params = json_encode($params);
        }
        $response = $this->getConnection()->put($uri, $params);
        $data = $this->handleBoxResponse($response, 'decoded');

        return $this->hydrate($resourceClass, $data);
    }
}
