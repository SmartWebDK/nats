<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * TODO: Missing interface description.
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
