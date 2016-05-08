<?php

namespace AceOugi;

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
            if (($header_value = array_shift($header_values)) !== null)
                header(sprintf('%s: %s', $header_name, $header_value));

            while (($header_value = array_shift($header_values)) !== null)
                header(sprintf('%s: %s', $header_name, $header_value), false);
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function sendBody(\Psr\Http\Message\ResponseInterface $response)
    {
        echo $response->getBody();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public static function send(\Psr\Http\Message\ResponseInterface $response)
    {
        @ob_clean();
        self::sendStatus($response);
        self::sendHeaders($response);
        self::sendBody($response);
        @ob_flush();
        flush();
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    public function __invoke(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, callable $next = null)
    {
        if ($next)
            $response = $next($request, $response);

        if ($response instanceof \Psr\Http\Message\ResponseInterface)
            $this->send($response);

        return $response;
    }
}
