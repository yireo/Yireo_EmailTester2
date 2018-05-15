<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Behaviour;

/**
 * EmailTester error trait
 */
trait Errorable
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function hasErrors() : bool
    {
        return (bool) empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
    /**
     * @param string $delimiter
     *
     * @return string
     */
    public function getErrorString(string $delimiter = '; ') : string
    {
        return implode($delimiter, $this->errors);
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->addError($error);
    }

    /**
     * @param string $error
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
    }
}
