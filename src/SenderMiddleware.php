<?php

namespace Ace;

class SenderMiddleware
{
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
            Sender::send($response);

        return $response;
    }
}
