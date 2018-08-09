<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Interface for the definition of a data container.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
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
