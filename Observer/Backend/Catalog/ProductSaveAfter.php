<?php declare(strict_types=1);

namespace Watch3r\AutoInStock\Observer\Backend\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $product;
    protected $stockStateInterface;
    protected $stockRegistry;

    public function __construct(
        \Magento\Catalog\Model\Product $product,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->product = $product;
        $this->stockStateInterface = $stockStateInterface;
        $this->stockRegistry = $stockRegistry;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            $productObserver = $observer->getProduct();
            if($productObserver->getTypeId() != 'configurable') {
                $productId = $productObserver->getId();
                $stockItem = $this->stockRegistry->getStockItem($productId);
                $productQty = $stockItem->getQty();
                if($productQty > 0) {
                    $stockItem->setData('is_in_stock',1);       
                } else {
                    $stockItem->setData('is_in_stock',0);
                }
                $stockItem->save();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
