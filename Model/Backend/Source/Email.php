<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Backend\Source;

/**
 * Class Yireo\EmailTester2\Model\Backend\Source\Email
 */
class Email
{
    public function __construct(
        \Magento\Email\Model\ResourceModel\Template\Collection $emailTemplateCollection,
        \Magento\Email\Model\Template\Config $emailConfig
    )
    {
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
        $options = array();
        $collection =  $this->emailTemplateCollection;

        if(!empty($collection)) {
            foreach($collection as $template) {
                /** @var \Magento\Email\Model\Template $templateCode */
                $templateCode = $template->getTemplateCode();
                if(empty($templateCode)) $templateCode = $template->getData('orig_template_code');
                $options[$templateCode]['value'] = $template->getTemplateId();
                $options[$templateCode]['label'] = $templateCode;
            }
            ksort($options);
        }

        $defaultOptions = $this->emailConfig->getAvailableTemplates();
        foreach($defaultOptions as $group => $option) {
            if(empty($option['value'])) continue;
            if(!empty($collection)) {
                $option['label'] = '['.$option['group'].'] '.$option['label'];
            }
            $options[] = $option;
        }

        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }
}