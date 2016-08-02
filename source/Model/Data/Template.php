<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 * @contributor Philipp Wiegel
 */

namespace Yireo\EmailTester2\Model\Data;

/**
 * Class Template
 */
class Template
{
    /** @var Mage_Core_Model_Email_Template */
    protected $templateModel;

    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->templateModel = Mage::getModel('core/email_template');
    }

    /**
     * Get all email template options
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        $options = array_merge($this->getTemplatesFromDatabase(), $this->getTemplatesFromDisc());

        return $options;
    }

    /**
     * @return array
     */
    public function getTemplatesFromDatabase()
    {
        $options = array();

        $templateCollection = $this->getEmailTemplateCollection();

        if (!empty($templateCollection)) {
            /** @var Mage_Core_Model_Email_Template $template */
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
    public function getTemplatesFromDisc()
    {
        $options = array();

        $defaultOptions = $this->getDefaultTemplatesAsOptionsArray();
        foreach ($defaultOptions as $option) {
            if (empty($option['value'])) continue;
            if (!empty($collection)) {
                $option['label'] = '[default] ' . $option['label'];
            }
            $options[] = $option;
        }

        return $options;
    }

    /**
     * @return Varien_Data_Collection_Db
     */
    protected function getEmailTemplateCollection()
    {
        return $this->templateModel->getResourceCollection()
            ->setOrder('template_code');
    }

    /**
     * @return array
     */
    protected function getDefaultTemplatesAsOptionsArray()
    {
        return $this->templateModel->getDefaultTemplatesAsOptionsArray();
    }
}