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

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Item as ButtonItem;
use Magento\Backend\Block\Widget\Button\ToolbarInterface as ButtonToolbarInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\EmailTester2\Block\Adminhtml\Overview\Form as OverviewForm;

/**
 * Class Overview
 *
 * @package Yireo\EmailTester2\Block\Adminhtml
 */
class Overview extends Template
{
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
    public function canRender(ButtonItem $item) : bool
    {
        return !$item->isDeleted();
    }

    /**
     * Create add button and grid blocks
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('form', OverviewForm::class);

        return parent::_prepareLayout();
    }
}
