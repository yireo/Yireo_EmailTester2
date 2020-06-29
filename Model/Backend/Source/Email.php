<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Backend\Source;

use Magento\Email\Model\ResourceModel\Template\Collection;
use Magento\Email\Model\Template;
use Magento\Email\Model\Template\Config;
use Magento\Framework\Data\OptionSourceInterface;

class Email implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Collection
     */
    private $emailTemplateCollection;

    /**
     * @var Config
     */
    private $emailConfig;

    /**
     * Email constructor.
     *
     * @param Collection $emailTemplateCollection
     * @param Config $emailConfig
     */
    public function __construct(
        Collection $emailTemplateCollection,
        Config $emailConfig
    ) {
        $this->emailTemplateCollection = $emailTemplateCollection;
        $this->emailConfig = $emailConfig;
    }

    /**
     * Return a list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];
        $collection = $this->emailTemplateCollection;

        if (!empty($collection)) {
            foreach ($collection as $template) {
                /** @var Template $templateCode */
                $templateCode = (string)$template->getTemplateCode();
                if (empty($templateCode)) {
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

            if (!empty($collection)) {
                $option['label'] = '[' . $option['group'] . '] ' . $option['label'];
            }
            $options[] = $option;
        }

        array_unshift($options, ['value' => '', 'label' => '']);

        return $options;
    }
}
