<?php
namespace RedCode\InstagramFeedBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author maZahaca
 */
class InstagramImage
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $imageSrc;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $imageThumbnail;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $userProfilePicture;

    /**
     * @var bool
     */
    protected $approved;

    /**
     * @var Tag
     */
    protected $tag;
}
