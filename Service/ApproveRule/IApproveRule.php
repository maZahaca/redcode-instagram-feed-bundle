<?php

namespace RedCode\InstagramFeedBundle\Service\ApproveRule;

/**
 * @author maZahaca
 */
interface IApproveRule
{
    /**
     * @param \stdClass $imageData image data from instagram
     * @return bool
     */
    public function check($imageData);
}