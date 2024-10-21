<?php
declare(strict_types=1);

namespace App\Service;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestIdProcessor
{
    private readonly string $runtimeUUID;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->runtimeUUID = self::generateUUIDv4();
    }

    public function __invoke(LogRecord $record)
    {
        $record['extra']['runtime_uuid'] = $this->getRuntimeUUID();

        return $record;
    }

    public function getRequestId(): ?string
    {
        return $this->requestStack->getCurrentRequest()?->headers->get('Request-Id');
    }

    public function getRuntimeUUID(): string
    {
        return $this->runtimeUUID;
    }

    private static function generateUUIDv4(): string
    {
        $uuid = unpack('h8time_low/S3octet/h12node', random_bytes(16));

        return sprintf(
            "%'08s-%'04x-%'04x-%'04x-%'012s",
            $uuid['time_low'],
            $uuid['octet1'],
            $uuid['octet2'] & 0x0FFF | 0x4000,
            $uuid['octet3'] & 0xBFFF | 0x8000,
            $uuid['node']
        );
    }
}
