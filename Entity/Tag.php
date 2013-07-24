<?php
namespace RedCode\InstagramFeedBundle\Entity;

/**
 * @author maZahaca
 */
class Tag
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    public function __toString()
    {
        return (string)$this->name;
    }
}
