<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Form;

use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Form\Item\ItemFactory;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Provider\ItemProviderInterface;
use Loki\AdminComponents\ViewModel\Options\StoreViewOptions;
use Magento\Framework\DataObject;
use Yireo\EmailTester2\Helper\Form as FormHelper;
use Yireo\EmailTester2\ViewModel\Options\EmailTemplateOptions;

class FormProvider implements FormProviderInterface, ItemProviderInterface
{
    public function __construct(
        private FormBuilder $formBuilder,
        private StoreViewOptions $storeViewOptions,
        private EmailTemplateOptions $emailTemplateOptions,
        private FormHelper $formHelper,
        private ItemFactory $itemFactory,
    ) {
    }

    public function getForm(): Form
    {
        return $this->formBuilder->createForm('emailtester2_form')
            ->addButton($this->formBuilder->createButton('send', 'Send Email', primary: true))
            ->addButton($this->formBuilder->createButton('preview', 'Preview Email', primary: true))
            ->addFieldset(
                $this->formBuilder->createFieldset('base', 'Generic Settings')
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'mail_from',
                            label: 'Mail From',
                            required: true,
                            fieldAttributes: [
                                'type' => 'email',
                            ]
                        )
                    )
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'mail_to',
                            label: 'Mail To',
                            required: true,
                            fieldAttributes: [
                                'type' => 'email',
                            ]
                        )
                    )
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'store_id',
                            label: 'Store View',
                            required: true,
                            fieldType: 'select',
                            options: $this->storeViewOptions
                        )
                    )
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'template',
                            label: 'Email Template',
                            required: true,
                            fieldType: 'select',
                            options: $this->emailTemplateOptions
                        )
                    )
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('customer', 'Customer Options')
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'customer_id',
                            label: 'Customer',
                            required: true,
                            fieldType: 'customer_select',
                        )
                    )
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('product', 'Product Options')
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'product_id',
                            label: 'Product',
                            required: true,
                            fieldType: 'product_select',
                        )
                    )
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('order', 'Order Options')
                    ->addField(
                        $this->formBuilder->createField(
                            name: 'order_id',
                            label: 'Order',
                            required: true,
                            fieldType: 'order_select',
                        )
                    )
            );
    }

    public function getItem(int|string $identifier): DataObject
    {
        return $this->itemFactory->create($this->formHelper->getFormData());
    }

    public function saveItem(DataObject $item): void
    {
    }

    public function deleteItem(DataObject $item): void
    {
    }

    public function duplicateItem(DataObject $item): void
    {
    }
}
