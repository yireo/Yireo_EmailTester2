<?php
/**
 * EmailTester2 plugin for Magento
 *
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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderLabel
 */
class OrderLabel extends AbstractLabel
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Context $context
     * @param Http $request
     * @param JsonFactory $resultJsonFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Http $request,
        JsonFactory $resultJsonFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context, $request, $resultJsonFactory);
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getLabel(): string
    {
        $id = $this->getId();
        if (!$id > 0) {
            throw new NoSuchEntityException(__('Empty ID'));
        }

        try {
            $order = $this->orderRepository->get($id);
        } catch (InvalidArgumentException $exception) {
            throw new NoSuchEntityException(__('Order not found'));
        }

        return $order->getIncrementId() . ' (' . $order->getCreatedAt() . ' / ' . $order->getCustomerEmail() . ')';
    }

    /**
     * @return string
     */
    protected function getEmptyLabel(): string
    {
        return 'No order found';
    }
}
