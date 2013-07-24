<?php
namespace RedCode\InstagramFeedBundle\Service\ApproveRule;

/**
 * @author maZahaca
 */ 
class DateRangeApproveRule implements IApproveRule
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    public function __construct(\DateTime $from, \DateTime $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * @inheritdoc
     */
    public function check($imageData)
    {
        return $imageData && $imageData->created_time && $this->from->getTimestamp() < $imageData->created_time && $this->to->getTimestamp() > $imageData->created_time;
    }
}
