<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot\Strategy;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\str;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \EmanueleMinotto\GuzzleSnapshot\Strategy\DoctrineCacheStrategy
 */
class DoctrineCacheStrategyTest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var DoctrineCacheStrategy
     */
    private $strategy;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(Cache::class);
        $this->strategy = new DoctrineCacheStrategy($this->cache);
    }

    public function testGetPromiseNotFulfilled()
    {
        $request = new Request('GET', 'http://www.example.com/');

        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with('28b67e05be4482c82f7fe28ea091ec6669e1cac4-response')
            ->willReturn(false)
        ;

        $this->assertNull($this->strategy->getPromise($request));
    }

    public function testGetPromiseFulfilled()
    {
        $request = new Request('GET', 'http://www.example.com/');
        $id = '28b67e05be4482c82f7fe28ea091ec6669e1cac4';

        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with($id.'-response')
            ->willReturn(true)
        ;

        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->with($id.'-response')
            ->willReturn(str(new Response()))
        ;

        $promise = $this->strategy->getPromise($request);
        $this->assertInstanceof(FulfilledPromise::class, $promise);
    }

    public function testStoreRequest()
    {
        $request = new Request('GET', 'http://www.example.com/');

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with('28b67e05be4482c82f7fe28ea091ec6669e1cac4-request', str($request))
        ;

        $this->strategy->storeRequest($request);
    }

    public function testStoreResponse()
    {
        $request = new Request('GET', 'http://www.example.com/');
        $response = new Response();

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with('28b67e05be4482c82f7fe28ea091ec6669e1cac4-response', str($response))
        ;

        $this->strategy->storeResponse($request, $response);
    }
}
