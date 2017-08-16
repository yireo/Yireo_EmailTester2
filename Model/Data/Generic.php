<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

/**
 * Class Generic
 */
class Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $currentStore;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $store;

    /**
     * @var \Yireo\EmailTester2\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * Generic constructor.
     *
     * @param \Yireo\EmailTester2\Helper\Output $outputHelper
     * @param \Magento\Backend\Model\Auth\Session\Proxy $session
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Backend\Model\Auth\Session\Proxy $session,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\App\ConfigInterface $config
    ) {
        $this->outputHelper = $outputHelper;
        $this->session = $session;
        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->config = $config;
    }

    /**
     * Get an array of all options defined in the extension settings
     *
     * @param string $type
     *
     * @return array
     */
    protected function getCustomOptions(string $type = ''): array
    {
        $customOptions = $this->getStoreConfig('emailtester/settings/custom_' . $type);
        if (empty($customOptions)) {
            return [];
        }

        $options = [];
        $customOptions = explode(',', $customOptions);
        foreach ($customOptions as $customOption) {
            $customOption = (int)trim($customOption);
            if ($customOption > 0) {
                $options[] = $customOption;
            }
        }

        return $options;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function isValidId($id): bool
    {
        if (empty($id)) {
            return false;
        }

        if (!is_numeric($id)) {
            return false;
        }

        if ($id < 1) {
            return false;
        }

        return true;
    }

    /**
     * Get the current store
     *
     * @return int|mixed
     * @throws \Exception
     */
    protected function getStoreId(): int
    {
        $storeId = (int)$this->request->getParam('store');
        if (!$storeId > 0) {
            $storeId = (int)$this->session->getData('emailtester.store');
        }

        return $storeId;
    }

    /**
     * @return int
     */
    protected function getWebsiteId(): int
    {
        $storeId = $this->getStoreId();
        if ($storeId > 0) {
            try {
                $store = $this->storeRepository->getById($storeId);
                return (int)$store->getWebsiteId();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                return 0;
            }
        }

        return 0;
    }

    /**
     * @param string $value
     *
     * @return null|string
     */
    protected function getStoreConfig(string $value)
    {
        return $this->config->getValue($value);
    }
}
