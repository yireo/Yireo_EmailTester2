<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer;

class Addressee
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $email;

    /**
     * Yireo_EmailTester_Model_Mailer_Addressee constructor
     * 
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        // @todo: Check if these values are still in same path
        $this->name = $scopeConfig->getValue(
            'trans_email/ident_general/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->email = $scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return array('name' => $this->name, 'email' => $this->email);
    }
}