<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Exception;

/**
 * Class PayloadBuildError
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class PayloadBuilderError extends \InvalidArgumentException implements ExceptionInterface
{

}
