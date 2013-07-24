<?php

namespace RedCode\InstagramFeedBundle\Service\ApproveRule;

/**
 * @author maZahaca
 */ 
class ApproveRuleManager implements IApproveRule
{
    private $rules = array();

    /**
     * @param array $rules
     * @throws \Exception
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $key => $rule) {
            switch($key) {
                case 'by_user':
                    if(is_array($rule)) {
                        $this->rules[] = new UsernameApproveRule($rule);
                    }
                    break;
                case 'by_date':
                    if(isset($rule['from']) && isset($rule['to'])) {
                        $this->rules[] = new DateRangeApproveRule(
                            new \DateTime($rule['from']),
                            new \DateTime($rule['to'])
                        );
                    }
                    break;
            }

        }
    }

    /**
     * @inheritdoc
     */
    public function check($imageData)
    {
        return
            count($this->rules) &&
            (bool)array_sum(
                array_map(
                    function($item) use ($imageData)
                    {
                        return (int)$item->check($imageData);
                    },
                    $this->rules
                )
            );
    }
}
