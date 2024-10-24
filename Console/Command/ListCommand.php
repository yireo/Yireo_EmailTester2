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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Yireo\EmailTester2\Model\Backend\Source\Email as EmailOptions;
use Symfony\Component\Console\Helper\Table;

class ListCommand extends Command
{
    /**
     * @var EmailOptions
     */
    private $emailOptions;

    /**
     * NewRuleCommand constructor.
     *
     * @param EmailOptions $emailOptions
     * @param string $name
     */
    public function __construct(
        EmailOptions $emailOptions,
        $name = null
    ) {
        $this->emailOptions = $emailOptions;
        parent::__construct($name);
    }

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this->setName('yireo_emailtester2:list');
        $this->setDescription('List all available transactional emails');
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $options = $this->emailOptions->toOptionArray();

        $headers = ['Value', 'Label'];
        $rows = [];
        foreach ($options as $option) {
            $rows[] = [
                $option['value'],
                $option['label'],
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($rows);
        $table->render();
    }
}
