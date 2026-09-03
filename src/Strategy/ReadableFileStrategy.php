<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot\Strategy;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ReadableFileStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function getPromise(RequestInterface $request): ?PromiseInterface
    {
        $id = sha1(Message::toString($request));

        $responseFile = sprintf('%s/%s-response.txt', $this->directory, $id);
        if (!file_exists($responseFile)) {
            return null;
        }

        try {
            return new FulfilledPromise(
                Message::parseResponse(file_get_contents($responseFile))
            );
        } catch (Throwable $exception) {
            return null;
        }
    }

    public function storeRequest(RequestInterface $request): void
    {
        $this->storeContent($request, 'request', Message::toString($request));
    }

    public function storeResponse(RequestInterface $request, ResponseInterface $response): void
    {
        $this->storeContent($request, 'response', Message::toString($response));
    }

    private function storeContent(RequestInterface $request, string $type, string $content): void
    {
        $id = sha1(Message::toString($request));
        $filename = sprintf('%s/%s-%s.txt', $this->directory, $id, $type);
        file_put_contents($filename, $content);
    }
}
