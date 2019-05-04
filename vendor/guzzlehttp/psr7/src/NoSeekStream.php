<?php

namespace _PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7;

use _PA88H63MC84HH6TR4VD\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that prevents a stream from being seeked
 */
class NoSeekStream implements \_PA88H63MC84HH6TR4VD\Psr\Http\Message\StreamInterface
{
    use \_PA88H63MC84HH6TR4VD\GuzzleHttp\Psr7\StreamDecoratorTrait;
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable()
    {
        return false;
    }
}