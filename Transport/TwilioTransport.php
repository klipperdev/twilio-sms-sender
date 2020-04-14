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

use Klipper\Component\SmsSender\Envelope;
use Klipper\Component\SmsSender\Mime\Sms;
use Klipper\Component\SmsSender\Transport\AbstractApiTransport;
use Klipper\Component\SmsSender\Transport\ErrorResult;
use Klipper\Component\SmsSender\Transport\Result;
use Klipper\Component\SmsSender\Transport\SuccessResult;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class TwilioTransport extends AbstractApiTransport
{
    /**
     * @var Client
     */
    private $twilio;

    /**
     * Constructor.
     *
     * @param null|string $accountSid Account Sid to authenticate with, defaults to $username
     * @param null|string $region     Region to send requests to, defaults to no region selection
     *
     * @throws
     */
    public function __construct(
        string $username,
        string $password,
        $accountSid = null,
        $region = null,
        EventDispatcherInterface $dispatcher = null,
        HttpClientInterface $client = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($client, $dispatcher, $logger);

        $this->twilio = new Client(
            $username,
            $password,
            $accountSid,
            $region,
            null !== $this->client ? new HttpClient($this->client) : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return sprintf(
            'api://%s@twilio?region=%s&accountSid=%s',
            $this->twilio->getUsername(),
            $this->twilio->getRegion(),
            $this->twilio->getAccountSid()
        );
    }

    protected function doSendSms(Sms $sms, Envelope $envelope, Result $result): void
    {
        $options = [
            'body' => $sms->getText(),
            'from' => $envelope->getFrom()->toString(),
        ];

        foreach ($envelope->getRecipients() as $recipient) {
            try {
                $strRecipient = $recipient->toString();
                $res = $this->twilio->messages->create($strRecipient, $options);

                $result->add(new SuccessResult($recipient, $res->toArray()));
            } catch (TwilioException $e) {
                $result->add(new ErrorResult($recipient, $e->getMessage(), (string) $e->getCode(), [], $e));
            }
        }
    }
}
