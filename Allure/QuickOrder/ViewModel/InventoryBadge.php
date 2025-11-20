<?php
namespace Allure\QuickOrder\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class InventoryBadge implements ArgumentInterface
{
    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockStateInterface
     */
    private $stockState;

    public function __construct(
        GetProductSalableQtyInterface $getProductSalableQty,
        StockResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        StockStateInterface $stockState
    ) {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
        $this->stockState = $stockState;
    }

    /**
     * Determine badge text for a product.
     *
     * @param ProductInterface $product
     * @return string
     */
    public function getBadge(ProductInterface $product): string
    {
        $qty = $this->getSalableQty($product);

        if ($qty <= 0) {
            return 'Out of Stock';
        }

        if ($qty > 20) {
            return 'In Stock';
        }

        return 'Limited';
    }

    /**
     * Fetch salable quantity using MSI with stock registry fallback.
     *
     * @param ProductInterface $product
     * @return float
     */
    private function getSalableQty(ProductInterface $product): float
    {
        try {
            $stockId = $this->stockResolver
                ->execute(
                    SalesChannelInterface::TYPE_WEBSITE,
                    $this->storeManager->getWebsite()->getCode()
                )
                ->getStockId();

            return (float)$this->getProductSalableQty->execute($product->getSku(), $stockId);
        } catch (\Exception $e) {
            $storeId = $product->getStoreId() ?: $this->storeManager->getStore()->getId();
            return (float)$this->stockState->getStockQty($product->getId(), $storeId);
        }
    }
}
