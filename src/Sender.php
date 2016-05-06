<?php

namespace AceOugi;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Sender
{
    /**
     * @param Response $response
     */
    public static function sendStatus(Response $response)
    {
        header(sprintf('HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reason_phrase = $response->getReasonPhrase()) ? ' '.$reason_phrase : ''
        ));
    }

    /**
     * @param Response $response
     */
    public static function sendHeaders(Response $response)
    {
        foreach ($response->getHeaders() as $header_name => $header_values)
        {
            if ($header_value = array_shift($header_values))
                header(sprintf('%s: %s', $header_name, $header_value));

            while ($header_value = array_shift($header_values))
                header(sprintf('%s: %s', $header_name, $header_value), false);
        }
    }

    /**
     * @param Response $response
     */
    public static function sendBody(Response $response)
    {
        echo $response->getBody();
    }

    /**
     * @param Response $response
     */
    public static function send(Response $response)
    {
        @ob_clean();
        self::sendStatus($response);
        self::sendHeaders($response);
        self::sendBody($response);
        @ob_flush();
        flush();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        if ($next)
            $response = $next($request, $response);

        if ($response instanceof Response)
            $this->send($response);

        return $response;
    }
}
