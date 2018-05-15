<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class PreviewButton
 */
class PreviewButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Preview Email'),
            'class' => 'save primary',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'preview'
                    ]
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
