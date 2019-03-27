# About
LiqPay does not have API for checkout page URL creator (online payment page). It is possible to
retrieve this URL from the LiqPay API response headers. This class solves this task.

# Usage
More information about parameters you can
find at [LiqPay documentation](https://www.liqpay.ua/documentation/en/api/aquiring/checkout/doc).

```php
$apiParams = [
    'public_key' => 'some_public_key',
    'private_key' => 'some_private_key'
];

$paymentParams = [
    'action' => 'pay',
    'amount' => '1',
    'currency' => 'USD',
    'description' => 'description text',
    'order_id' => 'order_id_1',
    'version' => '3'
];

$url = LiqPayCheckoutUrlCreator::create(
    $apiParams,
    $paymentParams
);
```