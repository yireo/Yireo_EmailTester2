<?php declare(strict_types=1);

/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2019 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
use Magento\Framework\Console\Cli;

class SendCommand extends Command
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var State
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
            'Recipient Email'
        );

        $this->addOption(
            'sender',
            null,
            InputOption::VALUE_REQUIRED,
            'Sender Email'
        );

        $this->addOption(
            'template',
            null,
            InputOption::VALUE_REQUIRED,
            'Template'
        );

        $this->addOption(
            'customer_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Customer ID'
        );

        $this->addOption(
            'order_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Order ID'
        );

        $this->addOption(
            'product_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Product ID'
        );

        $this->addOption(
            'store_id',
            null,
            InputOption::VALUE_OPTIONAL,
            'Store View ID'
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
        $data['email'] = $this->getStringOption($input, 'email', $this->config->getDefaultEmail());
        $data['sender'] = $this->getStringOption($input, 'sender', $this->config->getDefaultEmail());
        $data['template'] = $this->getStringOption($input, 'template', $this->config->getDefaultTransactional());
        $data['store_id'] = $this->getNumberOption($input, 'store_id', $this->getDefaultStoreId());
        $data['customer_id'] = $this->getNumberOption($input, 'customer_id', $this->config->getDefaultCustomer());
        $data['product_id'] = $this->getNumberOption($input, 'customer_id', $this->config->getDefaultProduct());
        $data['order_id'] = $this->getNumberOption($input, 'order_id', $this->config->getDefaultOrder());

        $this->state->setAreaCode(Area::AREA_FRONTEND);

        if ($data['store_id'] > 0) {
            $storeCode = $this->storeManager->getStore($data['store_id'])->getCode();
            $this->storeManager->setCurrentStore($storeCode);
        }

        $this->mailer->setData($data);
        $this->mailer->send();

        $output->writeln('<info>Mail has been sent</info>');
        return Cli::RETURN_SUCCESS;
    }

    /**
     * @return int
     */
    private function getDefaultStoreId(): int
    {
        return (int)$this->storeManager->getDefaultStoreView()->getId();
    }

    /**
     * @param Input $input
     * @param string $optionName
     * @param string $defaultValue
     * @return string
     */
    private function getStringOption(Input $input, string $optionName = '', $defaultValue = ''): string
    {
        $optionValue = trim((string)$input->getOption($optionName));
        if (!empty($optionValue)) {
            return $optionValue;
        }

        return (string)$defaultValue;
    }

    /**
     * @param Input $input
     * @param string $optionName
     * @param int $defaultValue
     * @return int
     */
    private function getNumberOption(Input $input, string $optionName = '', $defaultValue = 0): int
    {
        $optionValue = (int)$input->getOption($optionName);
        if (!empty($optionValue)) {
            return $optionValue;
        }

        return (int)$defaultValue;
    }
}
