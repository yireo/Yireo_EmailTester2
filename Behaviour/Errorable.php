<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Behaviour;

/**
 * EmailTester error trait
 * @todo: Log errors to log instead
 * @todo: Give exceptions
 */
trait Errorable
{
    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function hasErrors()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * @param string $delimiter
     *
     * @return string
     */
    public function getErrorString($delimiter = '')
    {
        return implode('; ', $this->errors);
    }

    /**
     * @param $error string
     */
    protected function addError($error)
    {
        $this->errors[] = $error;
    }
}
