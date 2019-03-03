<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot\Strategy;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface StrategyInterface
{
    public function getPromise(RequestInterface $request): ?PromiseInterface;

    public function storeRequest(RequestInterface $request): void;

    public function storeResponse(RequestInterface $request, ResponseInterface $response): void;
}
