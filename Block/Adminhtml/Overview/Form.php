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

namespace Yireo\EmailTester2\Block\Adminhtml\Overview;

/**
 * Class Form
 *
 * @package Yireo\EmailTester2\Block\Adminhtml\Overview
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Yireo\EmailTester2\Helper\Form
     */
    private $formHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Yireo\EmailTester2\Helper\Form $formHelper,
        array $data = []
    ) {
        $this->formHelper = $formHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fields to form and create template info form
     *
     * @return \Magento\Backend\Block\Widget\Form
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        // Generic fieldset
        $genericFieldset = $form->addFieldset(
            'generic_fieldset',
            ['legend' => __('Generic Options'), 'class' => 'fieldset-wide']
        );

        // Store switcher
        $options = $this->formHelper->getStoreOptions();
        $genericFieldset->addField(
            'store_id',
            'select',
            ['name' => 'store_id', 'label' => __('Store Scope'), 'values' => $options]
        );

        // Email field
        $genericFieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Mail To'), 'required' => true]
        );

        // Template options
        $options = $this->formHelper->getTemplateOptions();
        $genericFieldset->addField(
            'template',
            'select',
            ['name' => 'template', 'label' => __('Email Template'), 'required' => true, 'values' => $options]
        );

        // Customer fieldset
        $customerFieldset = $form->addFieldset(
            'customer_fieldset',
            ['legend' => __('Customer Value'), 'class' => 'fieldset-wide']
        );

        $customerFieldset->addType('id', '\Yireo\EmailTester2\Form\Element\Id');
        $customerFieldset->addField(
            'customer_id',
            'id',
            ['name' => 'customer_id', 'label' => __('Customer ID'), 'placeholder' => 'Numeric ID', 'required' => false]
        );

        $customerFieldset->addField(
            'customer_search',
            'text',
            ['name' => 'customer_search', 'label' => __('Customer Search'), 'placeholder' => 'Name or email', 'required' => false]
        );

        // Product fieldset
        $productFieldset = $form->addFieldset(
            'product_fieldset',
            ['legend' => __('Product Value'), 'class' => 'fieldset-wide']
        );

        $productFieldset->addField(
            'product_id',
            'text',
            ['name' => 'product_id', 'label' => __('Product ID'), 'placeholder' => 'Numeric ID', 'required' => false]
        );

        $productFieldset->addField(
            'product_search',
            'text',
            ['name' => 'product_search', 'label' => __('Product Search'), 'placeholder' => 'Name or SKU', 'required' => false]
        );

        // Order fieldset
        $orderFieldset = $form->addFieldset(
            'order_fieldset',
            ['legend' => __('Order Value'), 'class' => 'fieldset-wide']
        );

        $orderFieldset->addField(
            'order_id',
            'text',
            ['name' => 'order_id', 'label' => __('Order ID'), 'placeholder' => 'Numeric ID', 'required' => false]
        );

        $orderFieldset->addField(
            'order_search',
            'text',
            ['name' => 'order_search', 'label' => __('Order Search'), 'placeholder' => 'Increment ID, name or email', 'required' => false]
        );

        $form->setValues($this->getFormValues());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    private function getFormValues(): array
    {
        return $this->formHelper->getFormData();
    }
}
