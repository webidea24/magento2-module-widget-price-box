<?php

namespace Webidea24\WidgetProductPrice\Model\Widget\Source;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{

    /**
     * @var Config
     */
    private $eavConfig;

    public function __construct(
        Config $eavConfig
    )
    {
        $this->eavConfig = $eavConfig;
    }

    public function toOptionArray()
    {
        $attributes = $this->eavConfig->getEntityAttributes(Product::ENTITY);
        $options = [];
        foreach ($attributes as $attribute) {
            $options[$attribute->getAttributeCode()] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel() . ' (' . $attribute->getAttributeCode() . ')'
            ];
        }
        ksort($options);

        return $options;
    }
}
