<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * TODO: Missing class description.
 *
 * @internal
 */
class DataContainerDefinition implements DataContainerDefinitionInterface
{
    
    /**
     * @var FieldDefinitionInterface[]
     */
    private $original;
    
    /**
     * @var bool[]
     */
    private $fieldRequirements;
    
    /**
     * @var string[]
     */
    private $fields;
    
    /**
     * @var string[]
     */
    private $requiredFields;
    
    /**
     * @param FieldDefinitionInterface[] $fields
     */
    public function __construct(array $fields)
    {
        $this->original = $fields;
    }
    
    /**
     * @return string[]
     */
    public function getRequiredFields() : array
    {
        return $this->requiredFields ?? $this->requiredFields = $this->resolveRequiredFields();
    }
    
    /**
     * @return string[]
     */
    private function resolveRequiredFields() : array
    {
        return \array_keys(\array_filter($this->getFieldRequirements()));
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isFieldRequired(string $name) : bool
    {
        return $this->getFieldRequirements()[$name] ?? false;
    }
    
    /**
     * @return string[]
     */
    public function getFields() : array
    {
        return $this->fields ?? $this->fields = $this->resolveFields();
    }
    
    /**
     * @return string[]
     */
    private function resolveFields() : array
    {
        return \array_keys($this->getFieldRequirements());
    }
    
    /**
     * @return bool[]
     */
    private function getFieldRequirements() : array
    {
        return $this->fieldRequirements ?? $this->fieldRequirements = $this->resolveFieldRequirements();
    }
    
    /**
     * @return bool[]
     */
    private function resolveFieldRequirements() : array
    {
        $resolved = $this->convertToFieldRequiredMap($this->original);
        
        unset($this->original);
        
        return $resolved;
    }
    
    /**
     * @param FieldDefinitionInterface[] $fields
     *
     * @return bool[]
     */
    private function convertToFieldRequiredMap(array $fields) : array
    {
        $indexedFields = [];
        
        foreach ($fields as $field) {
            $indexedFields[$field->getName()] = $field->isRequired();
        }
        
        return $indexedFields;
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField(string $name) : bool
    {
        return \array_key_exists($name, $this->fieldRequirements);
    }
}
