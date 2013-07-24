<?php
namespace RedCode\InstagramFeedBundle\Service\ApproveRule;

/**
 * @author maZahaca
 */ 
class UsernameApproveRule implements IApproveRule
{
    /**
     * @var array
     */
    private $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @inheritdoc
     */
    public function check($imageData)
    {
        return $imageData && $imageData->user && in_array($imageData->user->username, $this->users);
    }
}
