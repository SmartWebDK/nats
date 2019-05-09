<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Error\Handler;

/**
 * Handles exceptions.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface ExceptionHandlerInterface
{
    
    /**
     * Handle the given exception.
     *
     * @param \Throwable $exception
     */
    public function handle(\Throwable $exception) : void;
}
