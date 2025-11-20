<?php
namespace Allure\QuickOrder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface QuickOrderItemInterface extends ExtensibleDataInterface
{
    /**
     * Get SKU.
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set SKU.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * Get quantity.
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Set quantity.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty);
}
