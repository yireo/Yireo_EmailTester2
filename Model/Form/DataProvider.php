<?php

namespace Yireo\EmailTester2\Model\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Api\Filter;
use Yireo\EmailTester2\Helper\Form as FormHelper;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * Constructor
     *
     * @param FormHelper $formHelper
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        FormHelper $formHelper,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->formHelper = $formHelper;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $formValues = $this->getFormValues();
        $this->loadedData = [0 => $formValues];

        return $this->loadedData;
    }

    /**
     * @return array
     */
    private function getFormValues(): array
    {
        return $this->formHelper->getFormData();
    }

    /**
     * Dummy method to satisfy the UiComponent mechanism
     *
     * @param Filter $filter
     *
     * @return mixed|null|void
     */
    public function addFilter(Filter $filter)
    {
        return null;
    }
}
