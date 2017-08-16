<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Behaviour;

/**
 * EmailTester data trait
 */
trait Datacontainable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getData($name = null)
    {
        if (empty($name)) {
            return $this->data;
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return false;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setData(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isDataEmpty(string $name) : bool
    {
        if (empty($this->data[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isDataLowerThanOne(string $name) : bool
    {
        if (!isset($this->data[$name])) {
            return true;
        }

        if ($this->data[$name] < 1) {
            return true;
        }

        return false;
    }
}
