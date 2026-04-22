<?php
/**
 * @package     Box
 * @subpackage  Box_Connection
 * @author      Chance Garcia
 * @copyright   (C)Copyright 2013 Chance Garcia, chancegarcia.com
 *
 * connection assumes a valid access token
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

namespace Box\Model\Connection;

use Box\Http\Response\BoxResponse;
use Box\Http\Response\BoxResponseInterface;
use Box\Model\Model;
use Box\Exception\BoxException;
use \CURLFile;
use Psr\Log\LoggerInterface;

/**
 * Class Connection
 * @package Box\Model
 * @todo add in method to access last curl info, error and error number for debugging
 */
class Connection extends Model implements ConnectionInterface
{
    protected mixed $responseType = "code";
    protected mixed $clientId = null;
    protected mixed $clientSecret = null;
    protected mixed $redirectUri = null;
    protected mixed $state = null;
    protected string $requestType = "GET";

    protected mixed $authenticationResponse = null;
    protected string $authenticationResponseClass = 'Box\Model\Connection\AuthenticationResponse';

    /**
     * @var array array of options with the options as the key and the option values as the value
     */
    protected array $curlOpts = [];

    private bool $disableSslVerification = false;

    public function __construct(?array $options = null)
    {
        parent::__construct($options);
        if (is_array($options) && array_key_exists('disableSslVerification', $options) && is_bool($options['disableSslVerification'])) {
            $this->disableSslVerification = $options['disableSslVerification'];
        }
    }

    // relooking over auth flow, we have to assume app is already authorized externally. rewrite to use tokens for connection
    // may need to store the tokens
    public function connect(): mixed
    {
        throw new BoxException('method not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function initCurl(): \CurlHandle
    {
        $ch = curl_init();
        $this->initCurlOpts($ch);
        return $ch;
    }

    /**
     * {@inheritdoc}
     */
    public function initCurlOpts(\CurlHandle $ch): \CurlHandle
    {
        // figure out how to log to verbose output to file. maybe make a box logger? or do output buffer capture?
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        // get full response with headers
        // http://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request
        curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        // note: disable should only be used for development purposes.
        curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , !$this->getDisableSslVerification());
        return $ch;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurlData(\CurlHandle $ch): BoxResponseInterface
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug('before curl_exec curl opts', array(
                __METHOD__ . ":" . __LINE__,
                var_export(curl_getinfo($ch), true),
            ));
        }
        $sResponse = curl_exec($ch);
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug('curl_exec response: ' . $sResponse, array(
                __METHOD__ . ":" . __LINE__,
            ));
        }

        // split curl result into header and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($sResponse, 0, $header_size);
        $body = substr($sResponse, $header_size) ?: "";

        return new BoxResponse($body, $header);
    }

    /**
     * {@inheritdoc}
     */
    public function initAdditionalCurlOpts(\CurlHandle $ch): \CurlHandle
    {
        $opts = $this->getCurlOpts();
        if (0 != count($opts))
        {
            foreach ($opts as $opt=>$optValue)
            {
                // CURLOPT_HTTPHEADER, CURLOPT_QUOTE, CURLOPT_HTTP200ALIASES and CURLOPT_POSTQUOTE require array or object arguments

                switch ($opt)
                {
                    case "CURLOPT_HTTPHEADER":
                    case "CURLOPT_QUOTE":
                    case "CURLOPT_HTTP200ALIASES":
                    case "CURLOPT_POSTQUOTE":
                        // throw exception so it doesn't throw a warning
                        if (!is_array($optValue))
                        {
                            $this->error(
                                array(
                                    'error' => 'curl opt (' . $opt . ') needs to be an array or object',
                                    'error_description' => 'curl opt (' . $opt . ') needs to be an array or object'
                                )
                            );
                        }
                        curl_setopt($ch, constant($opt), $optValue);
                        break;
                    default:
                        curl_setopt($ch, constant($opt), $optValue);
                        break;
                }

            }
        }
        return $ch;
    }

    /**
     * {@inheritdoc}
     */
    public function query(string $uri): BoxResponseInterface
    {
        $ch = $this->initCurl();
        $ch = $this->initCurlOpts($ch);
        curl_setopt($ch, CURLOPT_URL, $uri);
        $ch = $this->initAdditionalCurlOpts($ch);
        $data = $this->getCurlData($ch);

        return $data;
    }

    public function delete(string $uri): BoxResponseInterface
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug("delete uri: " . $uri, array(__METHOD__ . ":" . __LINE__));
        }
        throw new BoxException('stubbed method. please implement');
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $uri, array|string $params = []): BoxResponseInterface
    {
        $ch = $this->initCurl();
        $ch = $this->initCurlOpts($ch);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        if (is_array($params))
        {
            $postParams = $this->buildQuery($params);
            @trigger_error('the `params` value as an array will be deprecated in the future. Please provide a json encoded string',
                           E_USER_DEPRECATED);
        }
        else
        {
            $postParams = $params;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        $ch = $this->initAdditionalCurlOpts($ch);
        $data = $this->getCurlData($ch);

        return $data;

    }

    /**
     * {@inheritdoc}
     */
    public function post(string $uri, array|string $params = [], bool $nameValuePair = false): BoxResponseInterface
    {

        $ch = $this->initCurl();
        $ch = $this->initCurlOpts($ch);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, true);

        if ($nameValuePair)
        {
            $params = json_encode($params);
            @trigger_error('the `nameValuePair` switch will be deprecated in the future; all values will be json encoded values',
                           E_USER_DEPRECATED);
        }

        if (is_array($params))
        {
            $postParams = $this->buildQuery($params);
            @trigger_error('the `params` value as an array will be deprecated in the future. Please provide a json encoded string',
                           E_USER_DEPRECATED);
        }
        else
        {
            $postParams = $params;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        $ch = $this->initAdditionalCurlOpts($ch);
        $data = $this->getCurlData($ch);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function createCurlFile(string $pathToFile, string $mimeType, string $filename): CURLFile
    {
        return new CURLFile($pathToFile,$mimeType, $filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(string $file): mixed
    {
        $fInfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($fInfo, $file);
    }

    /**
     * {@inheritdoc}
     */
    public function postFile(string $uri, string $file, int $parentId = 0): array|BoxResponseInterface
    {
        // @todo allow Content-MD5 header to be set
        // Post 1-n files, each element of $files array assumed to be absolute
        // path to a file.  $files can be array (multiple) or string (one file).
        // Data will be posted in a series of POST vars named $file0, $file1...
        // $fileN

        $pathInfo = pathinfo($file);
        $filename = $file;
        if (array_key_exists('filename', $pathInfo))
        {
            $filename = $pathInfo['filename'] . "." . $pathInfo['extension'];
        }

        $mimeType = $this->getMimeType($file);

        $curlFile = $this->createCurlFile($file, $mimeType, $filename);

        $data=array(
            'file' => $curlFile,
            'parent_id' => $parentId
        );

        $ch = $this->initCurl();
        $ch = $this->initCurlOpts($ch);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $ch = $this->initAdditionalCurlOpts($ch);
        $data = $this->getCurlData($ch);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    /**
     * @param array|null $curlOpts
     */
    public function setCurlOpts(?array $curlOpts = null): void
    {
        if (!is_array($curlOpts))
        {
            $curlOpts = $curlOpts !== null ? array($curlOpts) : [];
        }
        $this->curlOpts = $curlOpts;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurlOpts(): array
    {
        return $this->curlOpts;
    }

    /**
     * @param string|null $authenticationResponseClass
     */
    public function setAuthenticationResponseClass(?string $authenticationResponseClass = null): void
    {
        $this->validateClass($authenticationResponseClass,'AuthenticationResponseInterface');
        $this->authenticationResponseClass = $authenticationResponseClass;
    }

    public function getAuthenticationResponseClass(): string
    {
        return $this->authenticationResponseClass;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId(mixed $clientId = null): void
    {
        $this->clientId = $clientId;
    }

    public function getClientId(): mixed
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientSecret
     */
    public function setClientSecret(mixed $clientSecret = null): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $redirectUri
     */
    public function setRedirectUri(mixed $redirectUri = null): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function getRedirectUri(): mixed
    {
        return $this->redirectUri;
    }

    /**
     * @param mixed $requestType
     */
    public function setRequestType(mixed $requestType = null): void
    {
        $this->requestType = $requestType;
    }

    public function getRequestType(): string
    {
        return $this->requestType;
    }

    /**
     * @param mixed $authenticationResponse
     */
    public function setAuthenticationResponse(mixed $authenticationResponse = null): void
    {
        $this->authenticationResponse = $authenticationResponse;
    }

    public function getAuthenticationResponse(): mixed
    {
        return $this->authenticationResponse;
    }

    /**
     * @param mixed $responseType
     */
    public function setResponseType(mixed $responseType = null): void
    {
        $this->responseType = $responseType;
    }

    public function getResponseType(): mixed
    {
        return $this->responseType;
    }

    /**
     * @param mixed $state
     */
    public function setState(mixed $state = null): void
    {
        $this->state = $state;
    }

    public function getState(): mixed
    {
        return $this->state;
    }

    public function getDisableSslVerification(): ?bool
    {
        return $this->disableSslVerification;
    }
}
