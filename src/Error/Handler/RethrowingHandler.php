<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Error\Handler;

/**
 * Re-throws provided exceptions.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
class RethrowingHandler implements ExceptionHandlerInterface
{
    
    /**
     * Handle the given exception.
     *
     * @param \Throwable $exception
     *
     * @throws \Throwable
     */
    public function handle(\Throwable $exception) : void
    {
        throw $exception;
    }
}
