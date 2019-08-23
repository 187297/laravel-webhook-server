<?php

namespace Spatie\WebhookServer\Tests\TestClasses;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;

class TestClient
{
    private $requests = [];

    private $useResponseCode = 200;

    private $throwRequestException = false;

    public function request(string $method, string $url, array $options)
    {
        $this->requests[] = compact('method', 'url', 'options');

        if ($this->throwRequestException) {
            throw new RequestException(
                'Request failed exception',
                new Request($method, $url),
                new Response(500)
            );
        }

        return new Response($this->useResponseCode);
    }

    public function assertRequestCount(int $expectedCount)
    {
        Assert::assertCount($expectedCount, $this->requests);

        return $this;
    }

    public function assertRequestsMade(array $expectedRequests)
    {
        $this->assertRequestCount(count($expectedRequests));

        foreach ($expectedRequests as $index => $expectedRequest) {
            foreach ($expectedRequest as $name => $value) {
                Assert::assertEquals($value, $this->requests[$index][$name]);
            }
        }
    }

    public function letEveryRequestFail()
    {
        $this->useResponseCode = 500;
    }

    public function throwRequestException()
    {
        $this->throwRequestException = true;
    }
}
