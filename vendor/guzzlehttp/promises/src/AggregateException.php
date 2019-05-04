<?php

namespace _PA88H63MC84HH6TR4VD\GuzzleHttp\Promise;

/**
 * Exception thrown when too many errors occur in the some() or any() methods.
 */
class AggregateException extends \_PA88H63MC84HH6TR4VD\GuzzleHttp\Promise\RejectionException
{
    public function __construct($msg, array $reasons)
    {
        parent::__construct($reasons, sprintf('%s; %d rejected promises', $msg, count($reasons)));
    }
}