<?php

namespace Ace;

class Sender
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function sendStatus(\Psr\Http\Message\ResponseInterface $response)
    {
        header(sprintf('HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reason_phrase = $response->getReasonPhrase()) ? ' '.$reason_phrase : ''
        ));
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function sendHeaders(\Psr\Http\Message\ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $header_name => $header_values)
        {
            $replace = true;
            foreach ($header_values as $header_value)
            {
                header(sprintf('%s: %s', $header_name, $header_value), $replace);
                $replace = false;
            }
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function sendBody(\Psr\Http\Message\ResponseInterface $response)
    {
        @ob_clean();
        echo $response->getBody();
        @ob_flush();
        flush();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function send(\Psr\Http\Message\ResponseInterface $response)
    {
        static::sendStatus($response);
        static::sendHeaders($response);
        static::sendBody($response);
    }
}
