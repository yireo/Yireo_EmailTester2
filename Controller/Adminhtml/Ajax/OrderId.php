<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use InvalidArgumentException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Yireo\EmailTester2\Model\Label\Order as OrderLabel;

/**
 * Class OrderId
 *
 * @package Yireo\EmailTester2\Controller\Ajax
 */
class OrderId extends AbstractId
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderLabel
     */
    private $orderLabel;

    /**
     * @param Context $context
     * @param Http $request
     * @param JsonFactory $resultJsonFactory
     * @param OrderLabel $orderLabel
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Http $request,
        JsonFactory $resultJsonFactory,
        OrderLabel $orderLabel,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context, $request, $resultJsonFactory);
        $this->orderRepository = $orderRepository;
        $this->orderLabel = $orderLabel;
    }

    /**
     * @return string
     */
    protected function getLabel(): string
    {
        $id = $this->getId();
        if (!$id > 0) {
            return 'No order found';
        }

        try {
            $order = $this->orderRepository->get($id);
        } catch (InvalidArgumentException $exception) {
            return 'No order found';
        }

        return $this->orderLabel->getLabel($order);
    }
}
