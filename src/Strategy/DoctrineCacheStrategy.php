<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot\Strategy;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class DoctrineCacheStrategy implements StrategyInterface
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function getPromise(RequestInterface $request): ?PromiseInterface
    {
        $id = sha1(Message::toString($request));

        if (!$this->cache->contains($id.'-response')) {
            return null;
        }

        try {
            return new FulfilledPromise(
                Message::parseResponse($this->cache->fetch($id.'-response'))
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

        $this->cache->save($id.'-'.$type, $content);
    }
}
