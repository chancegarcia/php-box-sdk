box.net-v2api-sdk
=================

Requires at least 8.0 (8.5 recommended)

php sdk for use with box.net v2 api (http://developers.box.com/)

Copyright (C) 2013-2026  Chance Garcia

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.

# usage

Note: Fluent setters (chaining) are deprecated as of v0.11.0 and will be removed in a future release. Please use them as non-fluent methods (do not rely on the return value).

## setup
```php
use Box\Model\Client\Client;
$boxClient = new Client();
$boxClient->setClientId($boxClientId);
$boxClient->setClientSecret($boxClientSecret);
```

## auth uri
```php
// see setup
$client->setAuthorizationCode($code);
$authUri = $client->buildAuthQuery();
$authUri .= '&state=' . $state;
```

## handle auth callback
```php
// see setup
$state = $request->get('state');
$code = $request->get('code');
try {
    $token = $client->getAccessToken();
    // store the token somehow
} catch (\Box\Exception\BoxException $e) {
    switch ($e->getError()) {
        case "invalid_grant":
            // do something to get another access code?
            $msg = 'invalid grant, try resending user to the auth uri (start of link process)';
            $logger->error($msg);
            break;
        case "invalid_request":
            // no break
        case "unauthorized_client":
            // no break
        case "invalid_client":
            // no break
        case "redirect_uri_mismatch":
            // no break
        case "insecure_redirect_uri":
            // no break
        case "invalid_redirect_uri":
            // no break
        default:
            break;
    }

    // bubble up exception
    throw $e;
}
```

## refresh token
```php
use Box\Model\Client\Client;
use Box\Model\Connection\Token\Token;
$boxClient = new Client();
$boxClient->setClientId($boxClientId);
$boxClient->setClientSecret($boxClientSecret);

$oToken = new Token();
$oToken->setAccessToken($accessToken);
$oToken->setRefreshToken($refreshToken);

$client->setToken($oToken);
$token = $client->refreshToken();
```
## upload file
```php
use Box\Model\Client\Client;

$uploadResponse = $client->uploadFileToBox($uploadFilePath);
```
# tasks
- [x] v0.4.0
  - [x] refactor `Connection::getCurlData` to return an HTTP Response object (use symfony/http-foundation)
    - [x] method to turn curl header string into header array to set in Response object
    - [x] method to determine status code given header string
    - [x] Existing calls in the `Client` class must still only analyse the body response
  - [x] `Service` class final methods analyze the Response object to determine error/response handling such as refresh token attempt
    - [x] Deprecate `Service::getFinalConnectionResult` and add warning of removal in v0.5.0
- [ ] v0.4.5
  - [ ] refactor `Service::getFromBox` to accept type `mapped`
  - [ ] refactor `Service::sendUpdateToBox` to accept type `mapped`
  - [ ] add [Error class](https://box-content.readme.io/reference#errors)
    - [ ] add handling for 409 `item_name_in_use` error
    - [ ] factory to create errors
  - [ ] Fix `StatusLine` parsing (reason phrase `Not Found` shows up as `Not`)
- [ ] v0.5.0
  - [ ] Add deprecation notice that the Client class will be removed. exact version removal undetermined
  - [ ] Add deprecation notice that the `Collection` class will be removed in v0.6.0 in favor of using `doctrine/collections`
  - [ ] use factories to create class returns instead passing class to map
  - [ ] methods to set `CURLOPT_SSL_VERIFYPEER` in `Connection` class
    - [ ] add deprecation notice that default value will be true in later release to allow time for migration from current behavior (false)
  - [ ] refactor
    - [ ] support PSR-7 (HTTP Messages)
      - [PSR-7](http://www.php-fig.org/psr/psr-7/)
      - [GitHub](https://github.com/php-fig/http-message)
      - [PSR-7 Example](https://mwop.net/blog/2015-01-26-psr-7-by-example.html)
      - [RFC 7231 Section 6](http://tools.ietf.org/html/rfc7231#section-6)
- [ ] v0.6.0
  - [ ] go to full composer dependency mode
  - [ ] add composer `doctrine/collections` requirement
  - [ ] use existing [OAuth2 for Box](https://github.com/stevenmaguire/oauth2-box) client library for our authentication
  - [ ] implement traits for mapping and logging in `BaseModel`, `Model` classes
  - [ ] implement `Retry-After` response header handling in abstract `Service`
  
tasks for version less than 0.4.0
- [ ] Client class
    - Note: token information as well as client id and secret are set from outside source/storage
    - [x] get access token given authorization code
    - [x] refresh token
    - [x] retrieve folder information from box given id
    - [x] get array of folder items (json decoded format)
    - [x] create new box folder
    - [ ] update folder information
    - [x] get folder collaborators
    - [x] add collaborator to a folder
    - [x] create shared link for folder
    - [x] copy box folder
    - [x] create authorization header for connection class using token
    - [x] destroy token
        - [ ] add error handling
    - [x] auth query
        - [x] build auth query uri
        - [x] set auth header for connection
            - [ ] allow additional headers to be merged because of header overwrite
    - [x] client id
    - [x] client secret
- [x] Collaboration class
    - [x] interface implemented
    - [x] validate status
- [ ] Collection class
    - figure out how to create dependency to an array collection library; not as separatable but better than maintaining our own/re-inventing the wheel
- [x] Connection class
    - [x] interface implemented
    - [x] ability to set additional curl opts
    - [x] send GET request
    - [x] return GET response
    - [x] send PUT request
    - [x] return PUT response
    - [x] send POST request
    - [x] return POST response
    - [ ] send DELETE request
    - [ ] return DELETE response
- [x] Token class
- [ ] Folder class
    - [x] interface implemented
- [ ] User class
    - [x] interface implemented
- [ ]  File class
    - [x] interface implemented
- [ ]  Comment class
- [ ]  Event class
- [ ]  Shared Items interaction
    - [x] create shared link (can be done via client)
- [ ]  Search
- [ ]  Task Class
- [ ] Unit Tests
    - [ ] Regression for current implementation
    - [ ] TDD for future implementation