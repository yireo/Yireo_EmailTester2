<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Block\Adminhtml;

use \Magento\Backend\Block\Template;

/**
 * Class Overview
 *
 * @package Yireo\EmailTester2\Block\Adminhtml
 */
class Overview extends Template
{
    /**
     * @var \Magento\Backend\Block\Widget\Button\ButtonList
     */
    private $buttonList;

    /**
     * @var \Magento\Backend\Block\Widget\Button\ToolbarInterface
     */
    private $toolbar;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     * @param \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList,
        \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar,
        array $data = []
    ) {
        $this->buttonList = $buttonList;
        $this->toolbar = $toolbar;
        parent::__construct($context, $data);
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getFormHtml() : string
    {
        return $this->getChildHtml('form');
    }

    /**
     * {@inheritdoc}
     */
    public function canRender(\Magento\Backend\Block\Widget\Button\Item $item) : bool
    {
        return !$item->isDeleted();
    }

    /**
     * Create add button and grid blocks
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addButtons();
        $this->addChild('form', 'Yireo\EmailTester2\Block\Adminhtml\Overview\Form');

        return parent::_prepareLayout();
    }

    /**
     * Add the buttons
     */
    private function addButtons()
    {
        $this->buttonList->add(
            'send',
            [
                'label' => __('Send Email'),
                'onclick' => "submitEmailTesterFormToUrl('emailtester_form', '" . $this->getSendUrl() . "')",
                'class' => 'send primary'
            ]
        );

        $this->buttonList->add(
            'preview',
            [
                'label' => __('Preview Email'),
                'class' => 'preview primary',
                'onclick' => "submitEmailTesterFormToUrl('emailtester_form', '" . $this->getPreviewUrl() . "')",
            ]
        );

        $this->toolbar->pushButtons($this, $this->buttonList);
    }

    /**
     * Return the URL to send the mail
     *
     * @return string
     */
    private function getSendUrl() : string
    {
        return $this->getUrl('*/*/send');
    }

    /**
     * Return the URL to output the mail
     *
     * @return string
     */
    private function getPreviewUrl() : string
    {
        return $this->getUrl('*/*/preview');
    }
}
