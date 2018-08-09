<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Definition of a field in a data container.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class FieldDefinition implements FieldDefinitionInterface
{
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var bool
     */
    private $required;
    
    /**
     * @param string    $name
     * @param bool|null $required
     */
    public function __construct(string $name, ?bool $required = null)
    {
        $this->name = $name;
        $this->required = $required ?? true;
    }
    
    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * @inheritDoc
     */
    public function isRequired() : bool
    {
        return $this->required;
    }
}
