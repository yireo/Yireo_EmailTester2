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

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\ProductAlert\Block\Email\Price;
use Magento\ProductAlert\Block\Email\Stock;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

/**
 * Class AlertGrid
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class AlertGrid implements VariablesInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var string
     */
    private $template;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * AlertGrid constructor.
     * @param State $appState
     * @param StoreRepositoryInterface $storeRepository
     * @param LayoutFactory $layoutFactory
     * @param Emulation $appEmulation
     * @param ScopeConfigInterface $scopeConfig
     * @param DesignInterface $design
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        State $appState,
        StoreRepositoryInterface $storeRepository,
        LayoutFactory $layoutFactory,
        Emulation $appEmulation,
        ScopeConfigInterface $scopeConfig,
        DesignInterface $design,
        ProductRepositoryInterface $productRepository
    ) {
        $this->appState = $appState;
        $this->storeRepository = $storeRepository;
        $this->layoutFactory = $layoutFactory;
        $this->appEmulation = $appEmulation;
        $this->scopeConfig = $scopeConfig;
        $this->design = $design;
        $this->productRepository = $productRepository;
    }

    /**
     *
     * @return array
     * @throws Exception
     */
    public function getVariables(): array
    {
        $variables = [];
        $variables['alertGrid'] = $this->getAlertGrid();

        return $variables;
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    private function getAlertGrid(): string
    {
        $store = $this->storeRepository->getById($this->storeId);
        $layoutFactory = $this->layoutFactory;

        $themeId = $this->scopeConfig->getValue(DesignInterface::XML_PATH_THEME_ID, 'store', $store);
        $this->design->setDesignTheme($themeId);

        $alertGrid = $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            function () use ($store, $layoutFactory) {
                /** @var Price $block */
                $layout = $layoutFactory->create();
                $block = $layout->createBlock($this->getBlockClass());

                if ($this->order) {
                    foreach ($this->order->getItems() as $item) {
                        $product = $this->productRepository->getById($item->getProductId());
                        $block->addProduct($product);
                    }
                }

                return $block->toHtml();
            }
        );

        return (string)$alertGrid;
    }

    /**
     *
     * @return string
     */
    private function getBlockClass(): string
    {
        if (strstr($this->template, 'stock')) {
            return Stock::class;
        }

        return Price::class;
    }

    /**
     *
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     *
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }
}
