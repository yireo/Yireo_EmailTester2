<?php declare(strict_types=1);

namespace Yireo\EmailTester2\Config\Frontend;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Funding extends Field
{
    protected $_template = 'Yireo_EmailTester2::funding.phtml';

    public function render(AbstractElement $element)
    {
        return $this->toHtml();
    }
}

