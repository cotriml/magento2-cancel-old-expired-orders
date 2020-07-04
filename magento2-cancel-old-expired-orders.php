<?php
header('Content-Type: application/json; charset=UTF-8');

/* ---- CONFIG ---- */
$token = 'here-goes-magento-2-api-token-with-get-orders-and-delete-orders-permission';   //https://www.mageplaza.com/devdocs/token-oauth-session-authentication-magento-2.html#token
$url = 'https://yoursite.com.br';                                                        //Your website url 
$endpoint = '/rest/V1/orders';
$daysAgo = 3;

$todayDateMinusDaysAgo = date('Y/m/d', strtotime('-' . $daysAgo . ' day'));
$filter = '';
$canceledOrders = array();

//Filtering all orders with creation date <= today's date - daysAgo
$filter .= 'searchCriteria[filterGroups][0][filters][0][field]=created_at';
$filter .= '&searchCriteria[filterGroups][0][filters][0][value]=' . $todayDateMinusDaysAgo;
$filter .= '&searchCriteria[filterGroups][0][filters][0][conditionType]=lteq';

//Filtering orders with PENDING status
$filter .= '&searchCriteria[filterGroups][1][filters][0][field]=status';
$filter .= '&searchCriteria[filterGroups][1][filters][0][value]=pending';
$filter .= '&searchCriteria[filterGroups][1][filters][0][conditionType]=eq';

//Finding orders to cancel
$ch = curl_init($url . $endpoint . '?' . $filter);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

$result = curl_exec($ch);
$ordersToCancel = json_decode($result);

if ($ordersToCancel->total_count > 0) {
    $ordersToCancel = $ordersToCancel->items;
} else {
    return;
}

// Loop to cancelar orders found 
foreach ($ordersToCancel as $order) {

    $orderId = $order->entity_id;
    $urlToCancel = $url . $endpoint . '/' . $orderId . '/cancel';

    $ch = curl_init($urlToCancel);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

    $canceledOrder = curl_exec($ch);

    if ($canceledOrder) {
        array_push($canceledOrders, $order->increment_id);
    }
}
echo 'Orders Canceled: ' . json_encode($canceledOrders) . '\n';
echo 'Total Orders Canceled: ' . count($canceledOrders);
