<?php

namespace Webidea24\WidgetProductPrice\Block\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductPrice extends Template implements BlockInterface, IdentityInterface
{

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    protected $_template = 'Webidea24_WidgetProductPrice::price-block.phtml';

    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
    }

    protected function _toHtml()
    {
        return $this->getProduct() ? parent::_toHtml() : '';
    }

    public function getPriceBlock()
    {
        /** @var Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if ($priceRender) {
            return $priceRender->render(
                FinalPrice::PRICE_CODE,
                $this->getProduct(),
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_VIEW,
                ]
            );
        }
    }

    public function getProduct(): ?Product
    {
        if (!$product = $this->getData('_product')) {
            $idPath = $this->getData('id_path');
            $exploded = explode('/', $idPath);
            if (!isset($exploded[1])) {
                return null;
            }

            try {
                $product = $this->productRepository->getById((int)$exploded[1]);
            } catch (NoSuchEntityException $e) {
                return null;
            }
            $this->setData('_product', $product);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $product;
    }

    public function getIdentities()
    {
        return ['widget_product_price'] + $this->getProduct()->getIdentities();
    }
}
