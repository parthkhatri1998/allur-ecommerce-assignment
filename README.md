Allure_QuickOrder
==================

Custom Magento 2 module providing:
- REST endpoint for quick order lookup by increment ID (`/V1/quickorder/:order_increment_id`).
- CLI command `bin/magento quickorder:history [email]` to list last 5 orders (all customers or filtered by email).
- Category product-listing inventory badge with color coding (green: In Stock, yellow: Limited, red: Out of Stock).

Setup
-----
1. Copy code to `app/code/Allure/QuickOrder`.
2. Run:
   - `bin/magento setup:upgrade`
   - `bin/magento cache:flush`
3. If in production mode:
   - `bin/magento setup:di:compile`

REST API
--------
- Route: `GET /V1/quickorder/{order_increment_id}`
- ACL: `Allure_QuickOrder::quickorder` (requires authenticated integration/admin with this permission).
- Response keys (DTO): `order_number`, `customer_name`, `customer_email`, `items` (sku, name, qty), `order_total`, `created_at`, `status`.

CLI
---
- List last 5 orders overall: `bin/magento quickorder:history`
- List last 5 orders for a customer: `bin/magento quickorder:history customer@example.com`

Frontend badge
--------------
- Injected via `view/frontend/layout/catalog_category_view.xml`.
- Template: `view/frontend/templates/inventory/badge.phtml`.
- Uses ViewModel `Allure\QuickOrder\ViewModel\InventoryBadge` to determine label based on salable qty/stock qty.

Notes
-----
- AI worklog maintained in `AI_LOG.md`.
