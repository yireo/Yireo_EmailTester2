<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Helper;

/**
 * Class \Yireo\EmailTester2\Helper\Form
 */
class Form extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource
    )
    {
        $this->emailSource = $emailSource;

        parent::__construct($context);
    }

    public function getTemplateOptions()
    {
        return $this->emailSource->toOptionArray();
    }
}
