<?php
namespace Allure\QuickOrder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface QuickOrderResultInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getOrderNumber(): string;

    /**
     * @param string $orderNumber
     * @return $this
     */
    public function setOrderNumber(string $orderNumber);

    /**
     * @return string
     */
    public function getCustomerName(): string;

    /**
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName(string $customerName);

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param string|null $customerEmail
     * @return $this
     */
    public function setCustomerEmail(?string $customerEmail);

    /**
     * @return \Allure\QuickOrder\Api\Data\QuickOrderItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Allure\QuickOrder\Api\Data\QuickOrderItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @return float
     */
    public function getOrderTotal(): float;

    /**
     * @param float $total
     * @return $this
     */
    public function setOrderTotal(float $total);

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt);

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status);
}
