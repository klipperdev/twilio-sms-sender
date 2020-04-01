<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bridge\SmsSender\Twilio\Transport;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Http\Client;
use Twilio\Http\Response;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class HttpClient implements Client
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * Constructor.@.
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string      $method
     * @param string      $url
     * @param array       $params
     * @param array       $data
     * @param array       $headers
     * @param null|string $user
     * @param null|string $password
     * @param null|int    $timeout
     *
     * @throws \ErrorException
     */
    public function request(
        $method,
        $url,
        $params = [],
        $data = [],
        $headers = [],
        $user = null,
        $password = null,
        $timeout = null
    ): Response {
        $options = [
            'auth_basic' => null !== $user && null !== $password ? [$user, $password] : null,
            'query' => $params,
            'headers' => $headers,
            'body' => empty($data) ? '' : $data,
            'timeout' => $timeout,
        ];

        try {
            $res = $this->client->request($method, $url, $options);

            return new Response($res->getStatusCode(), $res->getContent(), $res->getHeaders());
        } catch (ExceptionInterface $e) {
            throw new \ErrorException($e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }
}
