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

namespace Yireo\EmailTester2\Form\Element;

use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;

class Id extends AbstractElement
{
    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->setType('text');
        $this->setExtType('textfield');
    }

    /**
     * Get the HTML
     *
     * @return mixed
     */
    public function getElementHtml()
    {
        $attributes = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'placeholder' => $this->getPlaceholder(),
            'type' => 'text',
            'class' => 'input-text admin__control-text',
            'autocomplete' => 'off'
        ];

        $attributeHtml = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeHtml[] = $attributeName . '="' . $attributeValue . '"';
        }

        return '<input ' . implode($attributeHtml) . '>';
    }
}
