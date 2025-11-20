<?php
namespace Allure\QuickOrder\Model\Data;

use Allure\QuickOrder\Api\Data\QuickOrderItemInterface;
use Allure\QuickOrder\Api\Data\QuickOrderResultInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class QuickOrderResult extends AbstractExtensibleObject implements QuickOrderResultInterface
{
    /**
     * @inheritDoc
     */
    public function getOrderNumber(): string
    {
        return (string)$this->_get('order_number');
    }

    /**
     * @inheritDoc
     */
    public function setOrderNumber(string $orderNumber)
    {
        return $this->setData('order_number', $orderNumber);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName(): string
    {
        return (string)$this->_get('customer_name');
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName(string $customerName)
    {
        return $this->setData('customer_name', $customerName);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail(): ?string
    {
        $email = $this->_get('customer_email');
        return $email !== null ? (string)$email : null;
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail(?string $customerEmail)
    {
        return $this->setData('customer_email', $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array
    {
        return $this->_get('items') ?: [];
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items)
    {
        return $this->setData('items', $items);
    }

    /**
     * @inheritDoc
     */
    public function getOrderTotal(): float
    {
        return (float)$this->_get('order_total');
    }

    /**
     * @inheritDoc
     */
    public function setOrderTotal(float $total)
    {
        return $this->setData('order_total', $total);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return (string)$this->_get('created_at');
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return (string)$this->_get('status');
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status)
    {
        return $this->setData('status', $status);
    }
}
