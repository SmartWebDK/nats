<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Channel;

/**
 * Class Channel
 *
 * @api
 */
class Channel implements ChannelInterface
{
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * Channel constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }
}
