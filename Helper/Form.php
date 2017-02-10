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
class Form extends Data
{
    /**
     * @var \Yireo\EmailTester2\Model\Backend\Source\Email
     */
    protected $emailSource;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource,
        \Magento\Backend\Model\Session $session
    )
    {
        $this->emailSource = $emailSource;
        $this->session = $session;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getTemplateOptions()
    {
        return $this->emailSource->toOptionArray();
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        $data = array(
            'email' => $this->getConfigValue('default_email'),
            'template' => $this->getConfigValue('default_transactional'),
            'customer_id' => $this->getConfigValue('default_customer'),
            'order_id' => $this->getConfigValue('default_order'),
            'product_id' => $this->getConfigValue('default_product'),
        );

        $sessionData = $this->getDataFromSession();

        if (!empty($sessionData)) {
            foreach ($sessionData as $sessionName => $sessionValue) {
                if (isset($data[$sessionName]) && !empty($sessionValue)) {
                    $data[$sessionName] = $sessionValue;
                }
            }
        }

        return $data;
    }

    /**
     * @return mixed
     */
    protected function getDataFromSession()
    {
        return $this->session->getData('emailtester_values');
    }
}
