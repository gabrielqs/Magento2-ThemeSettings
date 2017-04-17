<?php

namespace Gabrielqs\ThemeSettings\Model\Config;

use \Magento\Framework\Data\OptionSourceInterface;

class Footers implements OptionSourceInterface
{
    /**
     * Returns payment methods available for installment calculation
     * The methods must be declared...
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Footer %1', 1)
            ],
            [
                'value' => 2,
                'label' => __('Footer %1', 2)
            ],
            [
                'value' => 3,
                'label' => __('Footer %1', 3)
            ],
            [
                'value' => 4,
                'label' => __('Footer %1', 4)
            ],
            [
                'value' => 5,
                'label' => __('Footer %1', 5)
            ],
            [
                'value' => 6,
                'label' => __('Footer %1', 6)
            ]
        ];
    }
}