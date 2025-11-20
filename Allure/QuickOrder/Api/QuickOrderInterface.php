<?php
namespace Allure\QuickOrder\Api;

/**
 * Quick order lookup service contract.
 */
interface QuickOrderInterface
{
    /**
     * Retrieve order details by increment ID.
     *
     * @param string $order_increment_id
     * @return \Allure\QuickOrder\Api\Data\QuickOrderResultInterface
     */
    public function getOrder(string $order_increment_id);
}
