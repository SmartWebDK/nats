<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * TODO: Missing interface description.
 *
 * @internal
 */
interface DataContainerDefinitionInterface
{
    
    /**
     * @return string[]
     */
    public function getRequiredFields() : array;
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isFieldRequired(string $name) : bool;
    
    /**
     * @return string[]
     */
    public function getFields() : array;
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField(string $name) : bool;
}
