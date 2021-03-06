<?php
/**
 * StatusApi
 * PHP version 5
 *
 * @category Class
 * @package  PicPay
*/

/**
 * PicPay - E-commerce Public API
 *
 * Public API
 *
 * OpenAPI spec version: 1.0
 * 
 */

namespace PicPay\SDK;

use _PA88H63MC84HH6TR4VD\GuzzleHttp\Client;
use _PA88H63MC84HH6TR4VD\GuzzleHttp\ClientInterface;
use _PA88H63MC84HH6TR4VD\GuzzleHttp\Exception\RequestException;
use _PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\MultipartStream;
use _PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\Request;
use _PA88H63MC84HH6TR4VD\GuzzleHttp\RequestOptions;
use PicPay\ApiException;
use PicPay\Configuration;
use PicPay\HeaderSelector;
use PicPay\ObjectSerializer;

/**
 * StatusApi Class Doc Comment
 *
 * @category Class
 * @package  PicPay
*/
class StatusApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation getStatus
     *
     * Status Request
     *
     * @param  string $reference_id seu id de referencia (required)
     *
     * @throws \PicPay\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \PicPay\modelPackage\StatusResponse200
     */
    public function getStatus($reference_id)
    {
        list($response) = $this->getStatusWithHttpInfo($reference_id);
        return $response;
    }

    /**
     * Operation getStatusWithHttpInfo
     *
     * Status Request
     *
     * @param  string $reference_id seu id de referencia (required)
     *
     * @throws \PicPay\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \PicPay\modelPackage\StatusResponse200, HTTP status code, HTTP response headers (array of strings)
     */
    public function getStatusWithHttpInfo($reference_id)
    {
        $returnType = '\PicPay\modelPackage\StatusResponse200';
        $request = $this->getStatusRequest($reference_id);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\PicPay\modelPackage\StatusResponse200',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\PicPay\modelPackage\Response401',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\PicPay\modelPackage\Response422',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\PicPay\modelPackage\Response500',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getStatusAsync
     *
     * Status Request
     *
     * @param  string $reference_id seu id de referencia (required)
     *
     * @throws \InvalidArgumentException
     * @return \_PA88H63MC84HH6TR4VD\GuzzleHttp\Promise\PromiseInterface
     */
    public function getStatusAsync($reference_id)
    {
        return $this->getStatusAsyncWithHttpInfo($reference_id)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getStatusAsyncWithHttpInfo
     *
     * Status Request
     *
     * @param  string $reference_id seu id de referencia (required)
     *
     * @throws \InvalidArgumentException
     * @return \_PA88H63MC84HH6TR4VD\GuzzleHttp\Promise\PromiseInterface
     */
    public function getStatusAsyncWithHttpInfo($reference_id)
    {
        $returnType = '\PicPay\modelPackage\StatusResponse200';
        $request = $this->getStatusRequest($reference_id);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getStatus'
     *
     * @param  string $reference_id seu id de referencia (required)
     *
     * @throws \InvalidArgumentException
     * @return \_PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\Request
     */
    protected function getStatusRequest($reference_id)
    {
        // verify the required parameter 'reference_id' is set
        if ($reference_id === null || (is_array($reference_id) && count($reference_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $reference_id when calling getStatus'
            );
        }

        $resourcePath = '/payments/{referenceId}/status';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // path params
        if ($reference_id !== null) {
            $resourcePath = str_replace(
                '{' . 'referenceId' . '}',
                ObjectSerializer::toPathValue($reference_id),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                []
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \_PA88H63MC84HH6TR4VD\GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \_PA88H63MC84HH6TR4VD\GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \_PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\build_query($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-picpay-token');
        if ($apiKey !== null) {
            $headers['x-picpay-token'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \_PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
