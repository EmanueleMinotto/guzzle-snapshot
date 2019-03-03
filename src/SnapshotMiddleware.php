<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot;

use EmanueleMinotto\GuzzleSnapshot\Strategy\StrategyInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SnapshotMiddleware
{
    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var bool
     */
    private $requestStorage = false;

    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $promise = $this->strategy->getPromise($request);
            if ($promise instanceof PromiseInterface) {
                return $promise;
            }

            if ($this->requestStorage) {
                $this->strategy->storeRequest($request);
            }

            return $handler($request, $options)->then(
                $this->onFulfilled($request)
            );
        };
    }

    public function setRequestStorage(bool $requestStorage): void
    {
        $this->requestStorage = $requestStorage;
    }

    private function onFulfilled(RequestInterface $request): callable
    {
        return function (ResponseInterface $response) use ($request) {
            $this->strategy->storeResponse($request, $response);

            return $response;
        };
    }
}
