<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Application\ErrorHandler\LogErrorHandler;
use App\Application\Exception\DomainException;
use App\Application\Exception\NotFoundException;
use App\Application\Exception\ValidationException;
use Assert\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ErrorHandler extends LogErrorHandler
{
    protected function respond(): ResponseInterface
    {
        $exception = $ex = $this->exception;

        switch (true) {
            case $exception instanceof ValidationException:
                $errors = [];
                foreach ($exception->getViolations() as $violation) {
                    $errors[] = [
                        'property' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                }
                return $this->asJson0(['errors' => $errors], 422);
            case $exception instanceof NotFoundException:
                $errors = [
                    [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                    ],
                ];
                return $this->asJson0(['errors' => $errors], 404);
            case $exception instanceof InvalidArgumentException:
                $errors = [
                    [
                        'property' => $exception->getPropertyPath(),
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                    ],
                ];
                return $this->asJson0(['errors' => $errors], 400);
            case $exception instanceof DomainException:
            case $exception instanceof \App\Application\Exception\InvalidArgumentException:
                $errors = [
                    [
                        'property' => null,
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode(),
                    ],
                ];
                return $this->asJson0(['errors' => $errors], 409);

            case $ex instanceof \InvalidArgumentException:
            case $ex instanceof \DomainException:
                $response = $this->asJson(['errors' => [$this->buildError($ex->getMessage())]], 409);

                break;
            default:
                $response = parent::respond();
        }

        return $response;
    }

    protected function logError(string $error): void
    {
        if ($this->exception instanceof HttpNotFoundException ||
            $this->exception instanceof HttpMethodNotAllowedException ||
            $this->exception instanceof ValidationException ||
            $this->exception instanceof NotFoundException ||
            $this->exception instanceof InvalidArgumentException ||
            $this->exception instanceof \App\Application\Exception\InvalidArgumentException ||
            $this->exception instanceof HttpUnauthorizedException
        ) {
            return;
        }

        parent::logError($error);
    }

    private function buildError(string $message, ?string $property = null): array
    {
        return [
            'message' => $message,
            'property' => $property,
        ];
    }

    private function asJson(array $data, int $status): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status);

        $response->getBody()->write(json_encode([
            'status' => $status,
            'errors' => isset($data['errors']) && \is_array($data['errors']) ? $data['errors'] : null,
        ], getenv('APP_ENV') === 'dev' ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : 0));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function asJson0(array $data, int $status): ResponseInterface
    {
        $json = json_encode($data, getenv('APP_ENV') === 'dev' ? JSON_PRETTY_PRINT : 0);
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($json);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
