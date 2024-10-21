<?php
declare(strict_types=1);

namespace App\Subscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseMetricListener implements EventSubscriberInterface
{
    final public const METRIC_NAME = 'route_duration';

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function responseLog(TerminateEvent $event): void
    {
        $requestTime = $event->getRequest()->server->get('REQUEST_TIME_FLOAT');
        $executionTime = microtime(true) - $requestTime;

        $this->logger->info(
            'Response Log',
            [
                'execution_time' => $executionTime,
                'service_name' => 'OM',
                'request' => [
                    'method' => $event->getRequest()->getMethod(),
                    'uri' => $event->getRequest()->getRequestUri(),
                    'route' => $event->getRequest()->attributes->get('_route'),
                    'headers' => (string) $event->getRequest()->headers,
                    'content' => $event->getRequest()->getContent(),
                ],
                'response' => [
                    'code' => $event->getResponse()->getStatusCode(),
                    'headers' => (string) $event->getResponse()->headers,
                    'content' => $event->getResponse()->getContent(),
                ],
            ]
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => [
                ['responseLog', 0],
            ],
        ];
    }
}
