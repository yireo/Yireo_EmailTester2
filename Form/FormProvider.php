<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Form;

use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Provider\FormProviderInterface;

class FormProvider implements FormProviderInterface
{
    public function __construct(
        private FormBuilder $formBuilder
    ) {
    }

    public function getForm(): Form
    {
        return $this->formBuilder->createForm('emailtester2_form')
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

    public function getData(): array
    {
        return [
            'mail_from' => 'jisse@yireo.com',
        ];
    }
}
