<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Yireo\EmailTester2\Model\Mailer\Variable\AlertGrid;
use Yireo\EmailTester2\Model\Mailer\Variable\Billing;
use Yireo\EmailTester2\Model\Mailer\Variable\Comment;
use Yireo\EmailTester2\Model\Mailer\Variable\Creditmemo;
use Yireo\EmailTester2\Model\Mailer\Variable\Customer;
use Yireo\EmailTester2\Model\Mailer\Variable\Invoice;
use Yireo\EmailTester2\Model\Mailer\Variable\Order;
use Yireo\EmailTester2\Model\Mailer\Variable\OrderVars;
use Yireo\EmailTester2\Model\Mailer\Variable\OtherVars;
use Yireo\EmailTester2\Model\Mailer\Variable\PaymentHtml;
use Yireo\EmailTester2\Model\Mailer\Variable\Product;
use Yireo\EmailTester2\Model\Mailer\Variable\Quote;
use Yireo\EmailTester2\Model\Mailer\Variable\Reason;
use Yireo\EmailTester2\Model\Mailer\Variable\Shipment;
use Yireo\EmailTester2\Model\Mailer\Variable\ShippingMsg;
use Yireo\EmailTester2\Model\Mailer\Variable\Store;

class VariableBuilder extends DataObject
{
    /**
     * @var array
     */
    private $variableModelNames = [
        'store' => Store::class,
        'order' => Order::class,
        'customer' => Customer::class,
        'product' => Product::class,
        'qoute' => Quote::class,
        'shipment' => Shipment::class,
        'invoice' => Invoice::class,
        'creditmemo' => Creditmemo::class,
        'billing' => Billing::class,
        'comment' => Comment::class,
        'payment_html' => PaymentHtml::class,
        'order_vars' => OrderVars::class,
        'shipping_msg' => ShippingMsg::class,
        'reason' => Reason::class,
        'alertGrid' => AlertGrid::class,
        'other_vars' => OtherVars::class,
    ];

    /**
     * @var VariableFactory
     */
    private $variableFactory;

    /**
     * @var VariableMethodFactory
     */
    private $variableMethodFactory;

    /**
     * @param VariableFactory $variableFactory
     * @param VariableMethodFactory $variableMethodFactory
     * @param array $data
     */
    public function __construct(
        VariableFactory $variableFactory,
        VariableMethodFactory $variableMethodFactory,
        $data = []
    ) {
        $this->variableFactory = $variableFactory;
        $this->variableMethodFactory = $variableMethodFactory;

        parent::__construct($data);
    }

    /**
     * Return all variables from underlying models
     *
     * @return array
     */
    public function getVariables(): array
    {
        $variables = $this->getVariablesFromVariableModels();
        $variables['template'] = $this->getData('template');

        return $variables;
    }

    /**
     * @return array
     */
    public function getVariableModelNames(): array
    {
        return $this->variableModelNames;
    }

    /**
     * @return array
     */
    private function getVariablesFromVariableModels(): array
    {
        $variables = [];
        foreach ($this->variableModelNames as $variableName => $variableModelName) {
            $variableModel = $this->variableFactory->create($variableModelName);

            try {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $variables = array_merge($variables, $this->callVariableModelMethods($variableModel, $variableName));
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        return $variables;
    }

    /**
     * @param AbstractVariableInterface $variableModel
     * @param string $variableName
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function callVariableModelMethods(AbstractVariableInterface $variableModel, string $variableName): array
    {
        foreach ($this->getData() as $name => $value) {
            $methodName = $this->variableMethodFactory->create($name, $variableModel);
            if ($methodName && $value) {
                $variableModel->$methodName($value);
            }
        }

        $variables = $this->retrieveVariables($variableModel, $variableName);

        return $variables;
    }

    /**
     * @param AbstractVariableInterface $variableModel
     * @param string $variableName
     *
     * @return array
     */
    private function retrieveVariables(AbstractVariableInterface $variableModel, string $variableName): array
    {
        $variables = [];

        if (method_exists($variableModel, 'getVariable')) {
            $variableValue = $variableModel->getVariable();
            $this->setData($variableName, $variableValue);
            $variables[$variableName] = $variableValue;
        }

        if (method_exists($variableModel, 'getVariables')) {
            $variableValues = $variableModel->getVariables();
            if (!empty($variableValues)) {
                foreach ($variableValues as $variableName => $variableValue) {
                    $this->setData($variableName, $variableValue);
                    $variables[$variableName] = $variableValue;
                }
            }
        }

        return $variables;
    }
}
