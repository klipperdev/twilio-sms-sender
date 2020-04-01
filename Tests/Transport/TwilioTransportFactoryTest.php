<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Bridge\SmsSender\Amazon\Tests\Transport;

use Klipper\Bridge\SmsSender\Twilio\Transport\TwilioTransport;
use Klipper\Bridge\SmsSender\Twilio\Transport\TwilioTransportFactory;
use Klipper\Component\SmsSender\Tests\TransportFactoryTestCase;
use Klipper\Component\SmsSender\Transport\Dsn;
use Klipper\Component\SmsSender\Transport\TransportFactoryInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class TwilioTransportFactoryTest extends TransportFactoryTestCase
{
    public function getFactory(): TransportFactoryInterface
    {
        return new TwilioTransportFactory($this->getDispatcher(), $this->getClient(), $this->getLogger());
    }

    public function supportsProvider(): iterable
    {
        yield [
            new Dsn('api', 'twilio'),
            true,
        ];

        yield [
            new Dsn('http', 'twilio'),
            true,
        ];

        yield [
            new Dsn('api', 'example.com'),
            false,
        ];
    }

    public function createProvider(): iterable
    {
        $client = $this->getClient();
        $dispatcher = $this->getDispatcher();
        $logger = $this->getLogger();

        yield [
            new Dsn('api', 'twilio', self::USER, self::PASSWORD),
            new TwilioTransport(self::USER, self::PASSWORD, null, null, $dispatcher, $client, $logger),
        ];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield [new Dsn('foo', 'twilio', self::USER, self::PASSWORD)];
    }

    public function incompleteDsnProvider(): iterable
    {
        yield [new Dsn('api', 'twilio', self::USER)];

        yield [new Dsn('api', 'twilio', null, self::PASSWORD)];
    }
}
