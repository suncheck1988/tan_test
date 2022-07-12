<?php

declare(strict_types=1);

namespace App\UI\Http\Action;

use App\Application\Service\Validator\Validator;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractAction
{
    protected ?ServerRequestInterface $request = null;
    protected ?ResponseInterface $response = null;

    protected Validator $validator;
    protected CommandBus $bus;
    protected LoggerInterface $logger;

    /**
     * @var array<string, string>
     */
    private array $args = [];

    public function __construct()
    {
    }

    /**
     * @param array<string, string> $args
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->handle($request);
    }

    public function init(
        Validator $validator,
        CommandBus $bus,
        LoggerInterface $logger
    ): void {
        $this->validator = $validator;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    abstract public function handle(ServerRequestInterface $request): ResponseInterface;

    protected function resolveArg(string $name): string
    {
        if (!isset($this->args[$name])) {
            throw new InvalidArgumentException("Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array<string, string> $headers
     */
    protected function asJson(array $data, int $status = 200, array $headers = []): ResponseInterface
    {
        $json = json_encode(
            $data,
            getenv('APP_ENV') === 'dev' ? JSON_PRETTY_PRINT : 0
        );

        /** @var ResponseInterface $response */
        $response = $this->response;
        $response->getBody()->write($json);

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    /**
     * @param array<string, string> $headers
     */
    protected function asJsonString(string $json, int $status = 200, array $headers = []): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->response;

        $response->getBody()->write($json);

        $response = $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    protected function asHtml(string $html, int $status = 200): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->response;
        $response->getBody()->write($html);

        return $response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($status);
    }

    /**
     * @param array<string, string> $headers
     */
    protected function asPdf(string $body, int $status = 200, array $headers = []): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->response;

        $response->getBody()->write($body);

        $response = $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withStatus($status);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    /**
     * @param array<string, string> $headers
     */
    protected function asCsv(string $body, int $status = 200, array $headers = []): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->response;

        $response->getBody()->write($body);

        $response = $response
            ->withHeader('Content-Type', 'text/csv')
            ->withStatus($status);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    protected function asEmpty(int $status = 204): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $this->response;
        return $response->withStatus($status);
    }
}
