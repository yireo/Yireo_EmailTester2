<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Form;

use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Provider\ItemProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

class FormProvider implements FormProviderInterface, ItemProviderInterface
{
    public function __construct(
        private FormBuilder $formBuilder,
        private DataObjectFactory $dataObjectFactory,
    ) {
    }

    public function getForm(): Form
    {
        return $this->formBuilder->createForm('emailtester2_form')
            ->addButton($this->formBuilder->createButton('send', 'Send Email', primary: true))
            ->addButton($this->formBuilder->createButton('preview', 'Preview Email', primary: true))
            ->addField($this->formBuilder->createField([
                'name' => 'mail_from',
                'type' => 'text',
                'required' => true,
                'label' => 'Mail From',
            ]))
            ->addFieldset(
                $this->formBuilder->createFieldset('base')
                    ->addField($this->formBuilder->createField([
                        'name' => 'mail_from',
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Mail From',
                    ]))
            );
    }

    public function getItem(int|string $identifier): DataObject
    {
        return $this->dataObjectFactory->create([
            'mail_from' => 'jisse@yireo.com',
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
