<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use InvalidArgumentException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterfaceFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

class Order implements VariablesInterface
{
    /**
     * @var int
     */
    private $orderId = 0;

    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @var int
     */
    private $productId = 0;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * @var Renderer
     */
    private $addressRenderer;

    /**
     * @var PhraseFactory
     */
    private $phraseFactory;
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;
    /**
     * @var OrderItemInterfaceFactory
     */
    private $orderItemFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var CartItemInterfaceFactory
     */
    private $quoteItemFactory;
    /**
     * @var ToOrderItem
     */
    private $toOrderItem;
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Order constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param CartItemInterfaceFactory $quoteItemFactory
     * @param ToOrderItem $toOrderItem
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param OrderItemInterfaceFactory $orderItemFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param View $customerViewHelper
     * @param PhraseFactory $phraseFactory
     * @param Renderer $addressRenderer
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository,
        CartItemInterfaceFactory $quoteItemFactory,
        ToOrderItem $toOrderItem,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemInterfaceFactory $orderItemFactory,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        View $customerViewHelper,
        PhraseFactory $phraseFactory,
        Renderer $addressRenderer
    ) {
        $this->orderRepository = $orderRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->toOrderItem = $toOrderItem;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemFactory = $orderItemFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerViewHelper = $customerViewHelper;
        $this->phraseFactory = $phraseFactory;
        $this->addressRenderer = $addressRenderer;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getVariables(): array
    {
        $order = $this->getOrder();
        $this->setProductInOrder($order);

        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        if (!$shippingAddress) {
            $shippingAddress = $billingAddress;
        }

        if (!$billingAddress) {
            $billingAddress = $shippingAddress;
        }

        $shippingAddressHtml = ($shippingAddress) ? $this->addressRenderer->format($shippingAddress, 'html') : '';
        $billingAddressHtml = ($billingAddress) ? $this->addressRenderer->format($billingAddress, 'html') : '';

        return [
            'order' => $order,
            'order_data' => $order,
            'formattedShippingAddress' => $shippingAddressHtml,
            'formattedBillingAddress' => $billingAddressHtml,
        ];
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    private function setProductInOrder(OrderInterface $order): bool
    {
        if (!$this->productId > 0) {
            return false;
        }

        $product = $this->productRepository->getById($this->productId, false, $this->storeId);

        $items = $order->getItems();
        foreach ($items as $item) {
            $item->setProductId($this->productId);
            $item->setProduct($product);
            $item->setName($product->getName());
            $item->setSku($product->getSku());
        }

        return true;
    }

    /**
     * @return OrderInterface
     * @throws LocalizedException
     */
    private function getOrder(): OrderInterface
    {
        $order = false;

        // Load the order by ID
        if (!empty($this->orderId)) {
            $order = $this->getOrderById((int)$this->orderId);
        }

        // Load the first order instead
        if (!$order || !$order->getEntityId() > 0) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchCriteria->setPageSize(1);
            $searchCriteria->setCurrentPage(1);

            try {
                $orders = $this->orderRepository->getList($searchCriteria)->getItems();
            } catch (InvalidArgumentException $exception) {
                $orders = null;
            }

            if (!empty($orders)) {
                return array_shift($orders);
            }
        }

        // Set the customer into the order
        $customer = $this->getCustomerById((int)$this->customerId);
        if ($order instanceof OrderInterface && $customer instanceof CustomerInterface) {
            $order->setCustomerId($customer->getId());
            $order->setCustomerName($this->customerViewHelper->getCustomerName($customer));
            $order->setCustomerFirstname($customer->getFirstname());
            $order->setCustomerLastname($customer->getLastname());
            $order->setCustomerNote('Some customer note');
            $order->setEmailCustomerNote('Some customer note');
            $order->setIsNotVirtual($order->getIsNotVirtual());
            $order->setCustomer($customer);
        }

        if (empty($order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        return $order;
    }

    /**
     * @param int $orderId
     *
     * @return bool|OrderInterface
     */
    private function getOrderById(int $orderId)
    {
        if (empty($orderId)) {
            return false;
        }

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (InvalidArgumentException $exception) {
            return false;
        } catch (NoSuchEntityException $exception) {
            return false;
        }

        return $order;
    }

    /**
     * @param int $customerId
     *
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomerById(int $customerId)
    {
        if (empty($customerId)) {
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }

        if (!$customer->getId() > 0) {
            return false;
        }

        return $customer;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * This method is called from the VariableBuilder to insert the current Store ID
     *
     * @param $storeId int
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId)
    {
        $this->productId = $productId;
    }
}
