<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bridge\SmsSender\Twilio\Tests\Transport;

use Klipper\Bridge\SmsSender\Twilio\Transport\HttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class HttpClientTest extends TestCase
{
    /**
     * @var HttpClientInterface|MockObject
     */
    private $client;

    /**
     * @var HttpClient
     */
    private $handler;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->handler = new HttpClient($this->client);
    }

    protected function tearDown(): void
    {
        $this->client = null;
        $this->handler = null;
    }

    /**
     * @throws
     */
    public function testSuccessRequest(): void
    {
        $method = 'POST';
        $uri = 'https://example.tld';
        $params = [];
        $data = [];
        $headers = [];
        $user = 'user';
        $password = 'password';
        $timout = 10;
        $requestOptions = [
            'auth_basic' => [$user, $password],
            'query' => [],
            'headers' => $headers,
            'body' => '',
            'timeout' => $timout,
        ];

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->expects(static::once())->method('getStatusCode')->willReturn(200);
        $response->expects(static::once())->method('getContent')->willReturn('{"foo": "bar"}');
        $response->expects(static::once())->method('getHeaders')->willReturn([]);

        $this->client->expects(static::once())
            ->method('request')
            ->with($method, $uri, $requestOptions)
            ->willReturn($response)
        ;

        $res = $this->handler->request($method, $uri, $params, $data, $headers, $user, $password, $timout);

        static::assertSame(200, $res->getStatusCode());
        static::assertSame(['foo' => 'bar'], $res->getContent());
        static::assertSame([], $res->getHeaders());
    }

    /**
     * @throws
     */
    public function testExceptionRequest(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('PREVIOUS EXCEPTION MESSAGE');

        $this->client->expects(static::once())
            ->method('request')
            ->willThrowException(new TransportException('PREVIOUS EXCEPTION MESSAGE'))
        ;

        $this->handler->request('POST', 'https://example.tld');
    }
}
