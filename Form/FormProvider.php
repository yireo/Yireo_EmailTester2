<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Form;

use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Provider\ItemProviderInterface;
use Loki\AdminComponents\ViewModel\Options\StoreViewOptions;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order;
use Yireo\EmailTester2\Helper\Form as FormHelper;
use Yireo\EmailTester2\ViewModel\Options\EmailTemplateOptions;

class FormProvider implements FormProviderInterface, ItemProviderInterface
{
    public function __construct(
        private FormBuilder $formBuilder,
        private DataObjectFactory $dataObjectFactory,
        private StoreViewOptions $storeViewOptions,
        private EmailTemplateOptions $emailTemplateOptions,
        private FormHelper $formHelper
    ) {
    }

    public function getForm(): Form
    {
        return $this->formBuilder->createForm('emailtester2_form')
            ->addButton($this->formBuilder->createButton('send', 'Send Email', primary: true))
            ->addButton($this->formBuilder->createButton('preview', 'Preview Email', primary: true))
            ->addFieldset(
                $this->formBuilder->createFieldset('base', 'Generic Settings')
                    ->addField($this->formBuilder->createField([
                        'name' => 'mail_from',
                        'required' => true,
                        'label' => 'Mail From',
                    ]))
                    ->addField($this->formBuilder->createField([
                        'name' => 'mail_to',
                        'required' => true,
                        'label' => 'Mail To',
                    ]))
                    ->addField($this->formBuilder->createField([
                        'name' => 'store_id',
                        'field_type' => 'select',
                        'required' => true,
                        'label' => 'Store View',
                        'options' => $this->storeViewOptions,
                    ]))
                    ->addField($this->formBuilder->createField([
                        'name' => 'template',
                        'field_type' => 'select',
                        'required' => true,
                        'label' => 'Email Template',
                        'options' => $this->emailTemplateOptions,
                    ]))
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('customer', 'Customer Options')
                    ->addField($this->formBuilder->createField([
                        'name' => 'customer_id',
                        'field_type' => 'customer_select',
                        'required' => true,
                        'label' => 'Customer',
                    ]))
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('product', 'Product Options')
                    ->addField($this->formBuilder->createField([
                        'name' => 'product_id',
                        'field_type' => 'product_select',
                        'required' => true,
                        'label' => 'Product',
                    ]))
            )
            ->addFieldset(
                $this->formBuilder->createFieldset('order', 'Order Options')
                    ->addField($this->formBuilder->createField([
                        'name' => 'order_id',
                        'field_type' => 'entity_select',
                        'required' => true,
                        'label' => 'Order',
                        'button_label' => 'Select Order',
                        'namespace' => 'sales_order_grid',
                        'provider' => OrderRepository::class,
                        'resource_model' => Order::class
                    ]))
            );
    }

    public function getItem(int|string $identifier): DataObject
    {
        return $this->dataObjectFactory->create([
            'data' => $this->formHelper->getFormData()
        ]);
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
