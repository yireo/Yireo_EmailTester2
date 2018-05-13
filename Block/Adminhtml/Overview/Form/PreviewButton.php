<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Block\Adminhtml\Overview\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

/**
 * Class PreviewButton
 */
class PreviewButton implements ButtonProviderInterface
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
            'label' => __('Preview Email'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'preview']],
                'form-role' => 'preview',
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
        return $this->urlBuilder->getUrl('*/*/preview');
    }
}
