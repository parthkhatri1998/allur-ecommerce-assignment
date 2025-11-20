<?php
namespace Allure\QuickOrder\Model\Data;

use Allure\QuickOrder\Api\Data\QuickOrderItemInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class QuickOrderItem extends AbstractExtensibleObject implements QuickOrderItemInterface
{
    /**
     * @inheritDoc
     */
    public function getSku(): string
    {
        return (string)$this->_get('sku');
    }

    /**
     * @inheritDoc
     */
    public function setSku(string $sku)
    {
        return $this->setData('sku', $sku);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->_get('name');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        return $this->setData('name', $name);
    }

    /**
     * @inheritDoc
     */
    public function getQty(): float
    {
        return (float)$this->_get('qty');
    }

    /**
     * @inheritDoc
     */
    public function setQty(float $qty)
    {
        return $this->setData('qty', $qty);
    }
}
