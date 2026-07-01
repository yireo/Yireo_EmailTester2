<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\ViewModel\Options;

use Magento\Email\Model\ResourceModel\Template\Collection;
use Magento\Email\Model\Template;
use Magento\Email\Model\Template\Config;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EmailTemplateOptions implements OptionSourceInterface, ArgumentInterface
{
    protected array $options = [];

    public function __construct(
        private readonly Collection $emailTemplateCollection,
        private readonly Config $emailConfig
    ) {
    }

    /**
     * Return a list of email templates
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!empty($this->options)) {
            return $this->options;
        }

        $options = [];
        $collection = $this->emailTemplateCollection;

        if ($collection->count() > 0) {
            foreach ($collection as $template) {
                /** @var Template $template */
                $templateCode = (string)$template->getTemplateCode();
                if (false === strlen($templateCode) > 0) {
                    $templateCode = (string)$template->getData('orig_template_code');
                }

                $options[$templateCode] = [];
                $options[$templateCode]['value'] = $template->getTemplateId();
                $options[$templateCode]['label'] = $templateCode;
            }
            ksort($options);
        }

        $defaultOptions = $this->emailConfig->getAvailableTemplates();
        foreach ($defaultOptions as $group => $option) {
            if (empty($option['value'])) {
                continue;
            }

            if ($collection->count() > 0) {
                $option['label'] = '['.$option['group'].'] '.$option['label'];
            }
            $options[] = $option;
        }

        array_unshift($options, ['value' => '', 'label' => '']);

        return $options;
    }
}
