# Products In Range
Magento 2 module for product search by price

## Installation
* Navigate to the root directory of your Magento 2 instance
* composer require nilayvy/module-products-in-range
```
* Enable the module through the Magento 2 CLI and run the database upgrade
```
bin/magento setup:upgrade
bin/magento setup:di:compile
```
* Redeploy static assets and flush cache
```
bin/magento setup:static-content:deploy && bin/magento cache:flush

## How to Use
The Products In Range module adds a new tab to the customer account section on the front end of the site. You can use this new section to search for products in the store filtered by price.

1. Log in as a customer on the Magento store front-end.
2. Navigate to the "My Account" page.
3. Click on the "Find Products in Range" link in the customer account navigation.
4. Enter a minimum price, maximum price, and sort direction in the form fields.
5. Click "Search" and the table on the page will be populated with products.

## Caveats
* The product grid will only display the first 10 products matching the search.
* Downloadable products and configurable products will show a quantity of "0" in the grid, since neither of these product types have a quantity that applies directly to them.
