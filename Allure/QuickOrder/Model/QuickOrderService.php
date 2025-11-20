<?php
namespace Allure\QuickOrder\Model;

use Allure\QuickOrder\Api\QuickOrderInterface;
use Allure\QuickOrder\Api\Data\QuickOrderItemInterfaceFactory;
use Allure\QuickOrder\Api\Data\QuickOrderResultInterfaceFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class QuickOrderService implements QuickOrderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var QuickOrderItemInterfaceFactory
     */
    private $quickOrderItemFactory;

    /**
     * @var QuickOrderResultInterfaceFactory
     */
    private $quickOrderResultFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        QuickOrderItemInterfaceFactory $quickOrderItemFactory,
        QuickOrderResultInterfaceFactory $quickOrderResultFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->quickOrderItemFactory = $quickOrderItemFactory;
        $this->quickOrderResultFactory = $quickOrderResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function getOrder(string $order_increment_id)
    {
        $orderIncrementId = $order_increment_id;
        $order = $this->getOrderByIncrementId($orderIncrementId);

        $result = $this->quickOrderResultFactory->create();
        $result->setOrderNumber($order->getIncrementId());
        $result->setCustomerName($this->getCustomerName($order));
        $result->setCustomerEmail($order->getCustomerEmail());
        $result->setItems($this->getItems($order));
        $result->setOrderTotal((float)$order->getGrandTotal());
        $result->setCreatedAt($order->getCreatedAt());
        $result->setStatus($order->getStatus());

        return $result;
    }

    /**
     * Load order using increment_id.
     *
     * @param string $orderIncrementId
     * @return OrderInterface
     */
    private function getOrderByIncrementId(string $orderIncrementId): OrderInterface
    {
        $filter = $this->filterBuilder
            ->setField('increment_id')
            ->setValue($orderIncrementId)
            ->setConditionType('eq')
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$filter])
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = reset($orders);

        if (!$order) {
            throw new NoSuchEntityException(__('Order with increment ID "%1" does not exist.', $orderIncrementId));
        }

        return $order;
    }

    /**
     * Build customer name string with fallbacks.
     *
     * @param OrderInterface $order
     * @return string
     */
    private function getCustomerName(OrderInterface $order): string
    {
        $fullName = trim(
            sprintf(
                '%s %s',
                (string)$order->getCustomerFirstname(),
                (string)$order->getCustomerLastname()
            )
        );

        if ($fullName !== '') {
            return $fullName;
        }

        if (method_exists($order, 'getCustomerName') && $order->getCustomerName()) {
            return (string)$order->getCustomerName();
        }

        $billing = $order->getBillingAddress();
        if ($billing && $billing->getName()) {
            return (string)$billing->getName();
        }

        return (string)__('Guest');
    }

    /**
     * Collect line items.
     *
     * @param OrderInterface $order
     * @return \Allure\QuickOrder\Api\Data\QuickOrderItemInterface[]
     */
    private function getItems(OrderInterface $order): array
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            $items[] = $this->quickOrderItemFactory->create()
                ->setSku($item->getSku())
                ->setName($item->getName())
                ->setQty((float)$item->getQtyOrdered());
        }

        return $items;
    }
}
