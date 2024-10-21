<?php

declare(strict_types=1);

namespace App\Exception;

use App\Dto\ErrorMessage;
use App\Dto\RestResponse;

class RestException extends \InvalidArgumentException
{
    private readonly RestResponse $restResponse;

    public function __construct($message = '', $code = 400, \Throwable $previous = null)
    {
        $this->restResponse = RestResponse::new($code);

        if (null !== $previous) {
            $this->restResponse->addError(ErrorMessage::fromThrowable($previous));
        }
        parent::__construct($message, $code, $previous);
    }

    public function getRestResponse(): RestResponse
    {
        return $this->restResponse;
    }
}
