<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer;

/**
 * EmailTester model
 */
class VariableBuilder extends \Magento\Framework\DataObject
{
    /**
     * @var array
     */
    private $variableNames = [
        'store',
        'order',
        'customer',
        'product',
        'quote',
        'shipment',
        'invoice',
        'creditmemo',
        'billing',
        'comment',
        'payment_html',
        'order_vars',
        'shipping_msg',
        'other_vars',
    ];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $data = []
    ) {
        $this->objectManager = $objectManager;

        parent::__construct($data);
    }

    /**
     * Return all variables from underlying models
     *
     * @return array
     */
    public function getVariables(): array
    {
        $variables = [];
        $variables['template'] = $this->getData('template');

        foreach ($this->variableNames as $variableName) {
            $className = ucfirst($this->dashesToCamelCase($variableName));
            $variableModel = $this->objectManager->create('\Yireo\EmailTester2\Model\Mailer\Variable\\' . $className);

            foreach ($this->getData() as $name => $value) {
                $methodName = 'set' . ucfirst($this->dashesToCamelCase($name));

                if (method_exists($variableModel, $methodName)) {
                    $variableModel->$methodName($value);
                }
            }

            if (method_exists($variableModel, 'getVariable')) {
                $variableValue = $variableModel->getVariable();
                $this->setData($variableName, $variableValue);
                $variables[$variableName] = $variableValue;
            }

            if (method_exists($variableModel, 'getVariables')) {
                $variableValues = $variableModel->getVariables();
                foreach ($variableValues as $variableName => $variableValue) {
                    $this->setData($variableName, $variableValue);
                    $variables[$variableName] = $variableValue;
                }
            }
        }

        return $variables;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function dashesToCamelCase(string $string): string
    {
        $string = explode('_', $string);
        $first = true;
        foreach ($string as &$v) {
            if ($first) {
                $first = false;
                continue;
            }
            $v = ucfirst($v);
        }

        return implode('', $string);
    }
}
