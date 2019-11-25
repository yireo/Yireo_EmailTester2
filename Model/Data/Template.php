<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 * @contributor Philipp Wiegel
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

/**
 * Class Template
 *
 * @deprecated There is no proper usage here
 */
class Template
{
    /** @var */
    private $templateModel = null;

    /**
     * Get all email template options
     *
     * @return array
     * @deprecated There is no proper usage here
     */
    public function getTemplateOptions() : array
    {
        $options = array_merge($this->getTemplatesFromDatabase(), $this->getTemplatesFromDisc());

        return $options;
    }

    /**
     * @return array
     */
    private function getTemplatesFromDatabase() : array
    {
        $options = [];

        $templateCollection = $this->getEmailTemplateCollection();

        if (!empty($templateCollection)) {
            /** @var $template */
            foreach ($templateCollection as $template) {
                $templateCode = $template->getTemplateCode();
                if (empty($templateCode)) {
                    $templateCode = $template->getData('orig_template_code');
                }

                $options[$templateCode]['value'] = $template->getTemplateId();
                $options[$templateCode]['label'] = $templateCode;
            }

            ksort($options);
        }

        return $options;
    }

    /**
     * @return array
     */
    private function getTemplatesFromDisc() : array
    {
        $options = [];

        $defaultOptions = $this->getDefaultTemplatesAsOptionsArray();
        foreach ($defaultOptions as $option) {
            if (empty($option['value'])) {
                continue;
            }

            if (!empty($collection)) {
                $option['label'] = '[default] ' . $option['label'];
            }
            $options[] = $option;
        }

        return $options;
    }

    /**
     * @return mixed
     */
    private function getEmailTemplateCollection()
    {
        return $this->templateModel->getResourceCollection()
            ->setOrder('template_code');
    }

    /**
     * @return array
     */
    private function getDefaultTemplatesAsOptionsArray() : array
    {
        return $this->templateModel->getDefaultTemplatesAsOptionsArray();
    }
}
