<?php

namespace App\Services\DataForSeoClient;

/**
 * PHP 7.4-8.0 REST Client build with cURL
 *
 * @author Fabio Agostinho Boris <fabioboris@gmail.com>
 * @added DataForSEO
 * @note Update to modern PHP standards
 */
class RestClient
{
    public string $host;        // the url to the rest server
    public ?int $port = null;
    public string $scheme;
    public string $postType = 'json';
    public int $timeout = 60;
    public int $connectionTimeout = 10;
    private ?string $token;       // Auth token
    private ?string $baUser;
    private ?string $baPassword;
    private ?string $baUa;
    public string $lastUrl = '';
    public $lastResponse = null;
    public $lastHttpCode = null;

    public function __construct(
        string $host,
        string $token = null,
        string $baUser = null,
        string $baPassword = null,
        string $baUserAgent = null
    ) {
        $arr_h = parse_url($host);
        if (isset($arr_h['port'])) {
            $this->port = (int)$arr_h['port'];
            $this->host = str_replace(":" . $this->port, "", $host);
        } else {
            $this->port = null;
            $this->host = $host;
        }

        if (isset($arr_h['scheme'])) {
            $this->scheme = $arr_h['scheme'];
        }

        $this->token = $token;
        $this->baUser = $baUser;
        $this->baPassword = $baPassword;
        $this->baUa = $baUserAgent;
    }

    /**
     * Make an HTTP GET request
     *
     * @param string $url
     * @param array $params
     * @throws RestClientException
     */
    public function get(string $url, array $params = [])
    {
        return $this->request('GET', $url, $params);
    }

    /**
     * Make an HTTP POST request
     *
     * @param string $url
     * @param array $params
     * @throws RestClientException
     */
    public function post(string $url, array $params = [])
    {
        return $this->request('POST', $url, $params);
    }

    /**
     * Make an HTTP PUT request
     *
     * @param string $url
     * @param array $params
     * @throws RestClientException
     */
    public function put(string $url, array $params = [])
    {
        return $this->request('PUT', $url, $params);
    }

    /**
     * Make an HTTP DELETE request
     *
     * @param string $url
     * @param array $params
     * @throws RestClientException
     */
    public function delete(string $url, array $params = [])
    {
        return $this->request('DELETE', $url, $params);
    }

    /**
     * Make an HTTP request using cURL
     *
     * @param string $verb
     * @param string $url
     * @param array $params
     * @throws RestClientException
     */
    private function request(string $verb, string $url, array $params = [])
    {
        $ch = curl_init();       // the cURL handler
        $url = $this->url($url); // the absolute URL
        $requestHeaders = [];
        if (!empty($this->token)) {
            $requestHeaders[] = "Authorization: $this->token";
        }

        if (!empty($this->baUser) && !empty($this->baPassword)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->baUser . ":" . $this->baPassword);
        }

        // encoded query string on GET
        switch (true) {
            case 'GET' == $verb:
                $url = $this->urlQueryString($url, $params);
                break;
            case in_array($verb, ['POST', 'PUT', 'DELETE']):
                if ($this->postType == 'json') {
                    $requestHeaders[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                }
        }

        // set the URL
        curl_setopt($ch, CURLOPT_URL, $url);
        $this->lastUrl = $url;

        // set the HTTP verb for the request
        switch ($verb) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        if (!empty($this->baUa)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->baUa);
        }

        if (!empty($this->port)) {
            curl_setopt($ch, CURLOPT_PORT, $this->port);
        }

        if (!empty($this->scheme) && 'https' === $this->scheme) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = $this->httpParseHeaders(substr($response, 0, $headerSize));
        $response = substr($response, $headerSize);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $contentError = curl_error($ch);
        //var_dump($content_error);

        curl_close($ch);

        if (strpos($contentType, 'json')) {
            $response = json_decode($response, true);
        }

        $this->lastResponse = $response;
        $this->lastHttpCode = $httpCode;

        switch (true) {
            case 'GET' == $verb:
                if ($httpCode !== 200) {
                    if (is_array($response)) {
                        $this->throwError($response, $httpCode);
                    }

                    $this->throwError(trim($contentError . "\n" . $response), $httpCode);
                }

                return $response;
            case in_array($verb, ['POST', 'PUT', 'DELETE']):
                if ($httpCode !== 303 && $httpCode !== 200) {
                    if (is_array($response)) {
                        $this->throwError($response, $httpCode);
                    }

                    $this->throwError(trim($contentError . "\n" . $response), $httpCode);
                }

                if (200 === $httpCode) {
                    return $response;
                }

                return str_replace(rtrim($this->host, '/') . '/', '', $headers['Location']);
        }
    }

    /**
     * Returns the absolute URL
     *
     * @param string|null $url
     * @return string
     */
    private function url(?string $url = null): string
    {
        $plainHost = rtrim($this->host, '/');
        $plainUrl = ltrim($url, '/');

        return "$plainHost:$this->port/$plainUrl";
    }

    /**
     * Returns the URL with encoded query string params
     *
     * @param string $url
     * @param array|null $params
     * @return string
     */
    private function urlQueryString(string $url, array $params = null): string
    {
        $qs = [];
        if ($params) {
            foreach ($params as $key => $value) {
                $qs[] = "$key=" . urlencode($value);
            }
        }

        $url = explode('?', $url);
        if (isset($url[1])) {
            $urlQs = $url[1];
        }

        $url = $url[0];
        if (isset($urlQs)) {
            $url = "$url?$urlQs";
        }

        if (count($qs)) {
            return "$url?" . implode('&', $qs);
        }

        return $url;
    }

    /**
     * Returns the absolute URL
     *
     * @param string $rawHeaders
     * @return array
     */
    private function httpParseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $key = '';

        foreach (explode("\n", $rawHeaders) as $h) {
            $h = explode(':', $h, 2);
            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }
                $key = $h[0];
            } else {
                if (str_starts_with($h[0], "\t")) {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
            }
        }

        return $headers;
    }

    /**
     * @throws RestClientException
     */
    private function throwError(mixed $response, int $httpCode): never
    {
        if (is_array($response) && array_key_exists('error', $response)) {
            if (isset($response['error']['message']) && isset($response['error']['code'])) {
                if (is_array($response['error']['message'])) {
                    throw new RestClientException(
                        implode("; ", $response['error']['message']),
                        (int)$response['error']['code'],
                        $httpCode,
                    );
                }

                throw new RestClientException(
                    $response['error']['message'],
                    (int)$response['error']['code'],
                    $httpCode,
                );
            }

            throw new RestClientException(implode("; ", $response), 0, $httpCode);
        }

        if (is_string($response)) {
            throw new RestClientException($response, 0, $httpCode);
        }

        throw new RestClientException(json_encode($response), 0, $httpCode);
    }
}
