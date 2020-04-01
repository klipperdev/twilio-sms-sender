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

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @internal
 */
final class HttpClientContents
{
    public static function getSuccessResponse(string $from = '+15017122661', string $to = '+15558675310'): string
    {
        return json_encode([
            'account_sid' => 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'api_version' => '2010-04-01',
            'body' => 'This is the ship that made the Kessel Run in fourteen parsecs?',
            'date_created' => 'Thu, 30 Jul 2015 20:12:31 +0000',
            'date_sent' => 'Thu, 30 Jul 2015 20:12:33 +0000',
            'date_updated' => 'Thu, 30 Jul 2015 20:12:33 +0000',
            'direction' => 'outbound-api',
            'error_code' => null,
            'error_message' => null,
            'from' => $from,
            'messaging_service_sid' => 'MGXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'num_media' => '0',
            'num_segments' => '1',
            'price' => -0.00750,
            'price_unit' => 'USD',
            'sid' => 'MMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'status' => 'sent',
            'subresource_uris' => [
                'media' => '/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Messages/SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Media.json',
            ],
            'to' => $to,
            'uri' => '/2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Messages/SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.json',
        ]);
    }

    public static function getErrorResponse(?string $message = null, ?int $code = null, ?int $status = null): string
    {
        $message = $message ?? 'The requested resource /2010-04-01/Accounts/ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX/Messages.json was not found';
        $code = $code ?? 20404;
        $status = $status ?? 404;

        return json_encode([
            'code' => $code,
            'message' => $message,
            'more_info' => 'https://www.twilio.com/docs/errors/'.$code,
            'status' => $status,
        ]);
    }
}
