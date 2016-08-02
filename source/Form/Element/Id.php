<?php

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
    )
    {
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
        $attributes = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'placeholder' => $this->getPlaceholder(),
            'type' => 'text',
            'class' => 'input-text admin__control-text',
            'autocomplete' => 'off'
        );

        $attributeHtml = array();
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeHtml[] = $attributeName.'="'.$attributeValue.'"';
        }

        return '<input '.implode($attributeHtml).'>';
    }
}
