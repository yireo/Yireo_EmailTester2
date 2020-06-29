<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer;

class VariableMethodFactory
{
    /**
     * @param string $name
     * @param $object
     * @return string
     */
    public function create($name, $object)
    {
        $methodName = $this->getSetterFromName($name);

        if (!method_exists($object, $methodName)) {
            return false;
        }

        return $methodName;
    }

    /**
     * @param $name
     * @return string
     */
    private function getSetterFromName($name)
    {
        return 'set' . ucfirst($this->dashesToCamelCase($name));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function dashesToCamelCase(string $string): string
    {
        $string = explode('_', $string);
        $first = true;
        foreach ($string as &$v) {
            if ($first) {
                $first = false;
                continue;
            }
            $v = ucfirst($v);
        }

        return implode('', $string);
    }
}
