<?php

declare(strict_types=1);

namespace Webparking\OpenAPIValidator;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\RequestValidator;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

trait ValidatesHttpMessagesAgainstDocs
{
    use MakesHttpRequests {
        MakesHttpRequests::call as parentCall;
    }

    private static RequestValidator $requestValidator;

    private static ResponseValidator $responseValidator;

    private static PsrHttpFactory $psrHttpFactory;

    /** @var string[] */
    protected array $ignoredUris = [];

    protected ?string $yamlPath = null;

    /**
     * @param string       $method
     * @param string       $uri
     * @param array<mixed> $parameters
     * @param array<mixed> $cookies
     * @param array<mixed> $files
     * @param array<mixed> $server
     * @param string|null  $content
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        if (\in_array($uri, $this->ignoredUris)) {
            return $this->parentCall($method, $uri, $parameters, $cookies, $files, $server, $content);
        }

        // The validators and httpFactory are cached for performance
        if (!isset(self::$requestValidator) || !isset(self::$responseValidator) || !isset(self::$psrHttpFactory)) {
            $validatorBuilder = new ValidatorBuilder();
            $validatorBuilder->fromYamlFile($this->yamlPath ?: base_path('docs/index.yaml'));

            self::$requestValidator = $validatorBuilder->getRequestValidator();
            self::$responseValidator = $validatorBuilder->getResponseValidator();

            $psr17Factory = new Psr17Factory();
            self::$psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        }

        self::$requestValidator->validate(
            self::$psrHttpFactory->createRequest(
                Request::create(
                    $this->prepareUrlForRequest($uri),
                    $method,
                    $parameters,
                    $cookies,
                    $files,
                    array_replace($this->serverVariables, $server),
                    $content
                )
            )
        );

        $response = $this->parentCall($method, $uri, $parameters, $cookies, $files, $server, $content);

        self::$responseValidator->validate(
            new OperationAddress('/'.ltrim($uri, '/'), strtolower($method)),
            self::$psrHttpFactory->createResponse(clone $response->baseResponse)
        );

        return $response;
    }
}
