<?php
session_start();
include "includes/functions.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['item_id'])) {
    $action = $_POST['action'];
    $item_id = $_POST['item_id'];
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    $num = sizeof($_SESSION['cart']);
    $found = false;
    
    for ($i = 0; $i < $num; $i++) {
        if ($_SESSION['cart'][$i]['item_id'] == $item_id) {
            $found = true;
            if ($action === 'increase') {
                // Enforce stock limit
                $itemData = get_item_id($item_id);
                if (!empty($itemData)) {
                    $available = (int)$itemData[0]['item_quantity'];
                    $currentQty = (int)$_SESSION['cart'][$i]['quantity'];
                    if ($currentQty < $available) {
                        $_SESSION['cart'][$i]['quantity'] = $currentQty + 1;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Insufficient stock for this item']);
                        exit;
                    }
                }
            } elseif ($action === 'decrease') {
                if ($_SESSION['cart'][$i]['quantity'] > 1) {
                    $_SESSION['cart'][$i]['quantity']--;
                } else {
                    // Remove item from cart if quantity becomes 0
                    unset($_SESSION['cart'][$i]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
            }
            break;
        }
    }
    
    if ($found) {
        // Get updated cart data
        $data = get_cart();
        $totalPrice = total_price($data);
        $totalItems = sizeof($_SESSION['cart']);
        
        // Get new quantity for the specific item
        $newQuantity = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cartItem) {
                if ($cartItem['item_id'] == $item_id) {
                    $newQuantity = $cartItem['quantity'];
                    break;
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'newQuantity' => $newQuantity,
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
