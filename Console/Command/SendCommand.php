<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2019 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Console\Input\InputOption;
use Yireo\EmailTester2\Config\Config;
use Yireo\EmailTester2\Model\Mailer;

/**
 * Class SendCommand
 *
 * @package Yireo\EmailTester2\Console\Command
 */
class SendCommand extends Command
{
    /**
     * @var
     */
    private $mailer;

    /**
     * @var
     */
    private $storeManager;

    /**
     * @var
     */
    private $config;

    /**
     * @var
     */
    private $state;

    /**
     * NewRuleCommand constructor.
     *
     * @param Mailer $mailer
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param State $state
     * @param string $name
     */
    public function __construct(
        Mailer $mailer,
        StoreManagerInterface $storeManager,
        Config $config,
        State $state,
        $name = null
    ) {
        $this->mailer = $mailer;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->state = $state;
        return parent::__construct($name);
    }

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this->setName('yireo_emailtester2:send');
        $this->setDescription('Send a test transactional email');

        $this->addOption(
            'email',
            null,
            InputOption::VALUE_REQUIRED,
            'Recipient Email',
            (string)$this->config->getDefaultEmail()
        );

        $this->addOption(
            'sender',
            null,
            InputOption::VALUE_REQUIRED,
            'Sender Email',
            (string)$this->config->getDefaultEmail()
        );

        $this->addOption(
            'template',
            null,
            InputOption::VALUE_REQUIRED,
            'Template',
            (string)$this->config->getDefaultTransactional()
        );

        $this->addOption(
            'customer_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Customer ID',
            (int)$this->config->getDefaultCustomer()
        );

        $this->addOption(
            'order_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Order ID',
            (int)$this->config->getDefaultOrder()
        );

        $this->addOption(
            'product_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Product ID',
            (int)$this->config->getDefaultProduct()
        );

        $this->addOption(
            'store_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Store View ID',
            (int)$this->getDefaultStoreId()
        );
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function execute(Input $input, Output $output)
    {
        $data = [];
        $data['email'] = trim($input->getOption('email'));
        $data['sender'] = trim($input->getOption('sender'));
        $data['template'] = trim($input->getOption('template'));
        $data['store_id'] = (int)$input->getOption('store_id');
        $data['customer_id'] = (int)$input->getOption('customer_id');
        $data['product_id'] = (int)$input->getOption('product_id');
        $data['order_id'] = (int)$input->getOption('order_id');

        $this->state->setAreaCode(Area::AREA_FRONTEND);

        if ($data['store_id'] > 0) {
            $storeCode = $this->storeManager->getStore($data['store_id'])->getCode();
            $this->storeManager->setCurrentStore($storeCode);
        }

        $this->mailer->setData($data);
        $this->mailer->send();

        $output->writeln('<info>Mail has been sent</info>');
    }

    /**
     * @return int
     */
    private function getDefaultStoreId(): int
    {
        return (int)$this->storeManager->getDefaultStoreView()->getId();
    }
}
