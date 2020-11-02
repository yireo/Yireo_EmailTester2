<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SendButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Send Email'),
            'class' => 'save primary',
            'id' => 'emailtesterSend',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'emailtesterSend'
                    ]
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
