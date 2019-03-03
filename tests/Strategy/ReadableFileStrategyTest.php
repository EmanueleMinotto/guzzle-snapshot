<?php

declare(strict_types=1);

namespace EmanueleMinotto\GuzzleSnapshot\Strategy;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\str;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \EmanueleMinotto\GuzzleSnapshot\Strategy\ReadableFileStrategy
 */
class ReadableFileStrategyTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $fileSystem;

    /**
     * @var DoctrineCacheStrategy
     */
    private $strategy;

    protected function setUp(): void
    {
        $this->fileSystem = vfsStream::setup();
        $this->strategy = new ReadableFileStrategy($this->fileSystem->url());
    }

    public function testGetPromiseNotFulfilled()
    {
        $request = new Request('GET', 'http://www.example.com/');

        $this->assertNull($this->strategy->getPromise($request));
    }

    public function testGetPromiseFulfilled()
    {
        $request = new Request('GET', 'http://www.example.com/');
        $id = '28b67e05be4482c82f7fe28ea091ec6669e1cac4';
        $response = new Response();

        vfsStream::newFile($id.'-response.txt')
            ->withContent(str($response))
            ->at($this->fileSystem)
        ;

        $promise = $this->strategy->getPromise($request);
        $this->assertInstanceof(FulfilledPromise::class, $promise);
    }

    public function testStoreRequest()
    {
        $request = new Request('GET', 'http://www.example.com/');
        $id = '28b67e05be4482c82f7fe28ea091ec6669e1cac4';
        $file = $id.'-request.txt';

        $this->assertFalse($this->fileSystem->hasChild($file));

        $this->strategy->storeRequest($request);

        $this->assertTrue($this->fileSystem->hasChild($file));
        $this->assertSame(
            str($request),
            file_get_contents($this->fileSystem->getChild($file)->url())
        );
    }

    public function testStoreResponse()
    {
        $request = new Request('GET', 'http://www.example.com/');
        $response = new Response();
        $id = '28b67e05be4482c82f7fe28ea091ec6669e1cac4';
        $file = $id.'-response.txt';

        $this->assertFalse($this->fileSystem->hasChild($file));

        $this->strategy->storeResponse($request, $response);

        $this->assertTrue($this->fileSystem->hasChild($file));
        $this->assertSame(
            str($response),
            file_get_contents($this->fileSystem->getChild($file)->url())
        );
    }
}
