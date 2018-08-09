<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Interface for the definition of a field in a data container.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
interface FieldDefinitionInterface
{
    
    /**
     * @return string
     */
    public function getName() : string;
    
    /**
     * @return bool
     */
    public function isRequired() : bool;
}
