<?php

namespace _PA88H63MC84HH6TR4VD\GuzzleHttp\Exception;

use _PA88H63MC84HH6TR4VD\Psr\Http\Message\StreamInterface;
/**
 * Exception thrown when a seek fails on a stream.
 */
class SeekException extends \RuntimeException implements \_PA88H63MC84HH6TR4VD\GuzzleHttp\Exception\GuzzleException
{
    private $stream;
    public function __construct(\_PA88H63MC84HH6TR4VD\Psr\Http\Message\StreamInterface $stream, $pos = 0, $msg = '')
    {
        $this->stream = $stream;
        $msg = $msg ?: 'Could not seek the stream to position ' . $pos;
        parent::__construct($msg);
    }
    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }
}