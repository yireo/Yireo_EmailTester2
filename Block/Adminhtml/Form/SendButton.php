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
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * SendButton constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    )
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Send Email'),
            'class' => 'save primary',
            'data_attribute' => [
                'action' => 'preview',
                'mage-init' => ['button' => ['event' => 'send']],
                'form-role' => 'send',
            ],
            'sort_order' => 80,
        ];
    }

    /**
     * Return the URL to send the mail
     *
     * @return string
     */
    private function getUrl() : string
    {
        return $this->urlBuilder->getUrl('*/*/send');
    }
}
