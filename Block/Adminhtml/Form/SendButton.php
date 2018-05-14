<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class SendButton
 */
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
            'data_attribute' => [
                'action' => 'send',
                'mage-init' => ['button' => ['event' => 'send']],
                'form-role' => 'send',
            ],
            'sort_order' => 80,
        ];
    }
}
