<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Form;

use Loki\AdminComponents\Provider\ArrayProviderInterface;

class FormProvider implements ArrayProviderInterface
{

    public function getColumns(): array
    {
        return [
            'mail_from' => [
                'name' => 'mail_from',
                'type' => 'text',
                'required' => true,
                'label' => 'Mail From',
            ]
        ];
    }

    public function getData(): array
    {
        return [
            'mail_from' => 'jisse@yireo.com',
        ];
    }
}
