<?php
include "includes/head.php"
?>
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f8fafc;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }
    
    .cart-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    
    .cart-header {
        background: transparent;
        color: #1f2937;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: none;
        text-align: center;
    }
    
    .cart-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        color: #000000;
    }
    
    .cart-header .cart-icon {
        background: #f3f4f6;
        color: #374151;
        padding: 0.75rem;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    
    .cart-items {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .cart-item {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }
    
    .cart-item:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
    
    .item-image {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .cart-item:hover .item-image img {
        transform: scale(1.05);
    }
    
    .item-details {
        flex: 1;
        min-width: 0;
    }
    
    .item-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
        line-height: 1.4;
    }
    
    .item-brand {
        display: inline-block;
        background: #059669;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }
    
    .item-stock {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }
    
    .stock-in {
        color: #059669;
    }
    
    .stock-out {
        color: #ef4444;
        font-weight: 600;
    }
    
    .stock-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }
    
    .item-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #059669;
        margin: 0.5rem 0;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .quantity-btn {
        background: #f3f4f6;
        color: #374151;
        border: 2px solid #e5e7eb;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .quantity-btn:hover {
        background: #059669;
        color: white;
        border-color: #059669;
        transform: scale(1.05);
    }
    
    .quantity-btn:active {
        transform: scale(0.95);
    }
    
    .quantity-display {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        color: #374151;
        min-width: 50px;
        text-align: center;
    }
    
    .item-quantity {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    
    .quantity-badge {
        background: #f3f4f6;
        color: #374151;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .delete-btn {
        background: #fef2f2;
        color: #ef4444;
        border: 2px solid #fecaca;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .delete-btn:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
        transform: translateY(-1px);
    }
    
    .cart-summary {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    
    .summary-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .summary-icon {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 0.75rem;
        border-radius: 12px;
        font-size: 1.25rem;
    }
    
    .summary-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    
    .total-amount {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .total-items {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .total-price {
        font-size: 2.5rem;
        font-weight: 800;
        color: #059669;
        margin: 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 1rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        border: 2px solid transparent;
        cursor: pointer;
    }
    
    .btn-danger {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fecaca;
    }
    
    .btn-danger:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        border-color: #059669;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25);
    }
    
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    .empty-cart-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1.5rem;
    }
    
    .empty-cart h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #374151;
        margin: 0 0 1rem 0;
    }
    
    .empty-cart p {
        font-size: 1.125rem;
        color: #6b7280;
        margin: 0 0 2rem 0;
    }
    
    .shop-now-btn {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .shop-now-btn:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25);
    }
    
    @media (max-width: 768px) {
        .cart-container {
            padding: 0 1rem;
            margin: 1rem auto;
        }
        
        .cart-header {
            padding: 1.5rem;
        }
        
        .cart-header h1 {
            font-size: 1.5rem;
        }
        
        .cart-item {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .item-image {
            width: 100px;
            height: 100px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
        
        .total-price {
            font-size: 2rem;
        }
    }
    
    .snackbar {
        visibility: hidden;
        min-width: 300px;
        margin-left: -150px;
        background-color: #333;
        color: #fff;
        text-align: left;
        border-radius: 8px;
        padding: 16px 20px;
        position: fixed;
        z-index: 1000;
        left: 50%;
        bottom: 30px;
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .snackbar.show {
        visibility: visible;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    .snackbar.error {
        background-color: #f44336;
    }

    .snackbar.warning {
        background-color: #ff9800;
    }

    .snackbar.success {
        background-color: #4caf50;
    }

    #snackbar-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #snackbar-close:hover {
        opacity: 0.7;
    }

    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }

    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }

    .stock-alert {
        margin: 1rem 0;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
    }

    .stock-alert i {
        font-size: 1.2rem;
    }
</style>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>
                <span class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                My Shopping Cart
            </h1>
        </div>

        <?php
        if (!empty($_SESSION['cart'])) {
            $data = get_cart();
            delete_from_cart();
            $num = sizeof($data);
        ?>
            <div class="cart-items">
                <?php
                for ($i = 0; $i < $num; $i++) {
                    if (isset($data[$i])) {
                ?>
                        <div class="cart-item" data-item-id="<?php echo $data[$i][0]['item_id'] ?>">
                            <div class="item-image">
                                <img src="images/<?php echo $data[$i][0]['item_image'] ?>" alt="<?php echo $data[$i][0]['item_title'] ?>">
                            </div>
                            <div class="item-details">
                                <h3 class="item-title"><?php echo $data[$i][0]['item_title'] ?></h3>
                                <div class="item-brand"><?php echo $data[$i][0]['item_brand'] ?></div>
                                <div class="item-stock <?php echo $data[$i][0]['item_quantity'] > 0 ? 'stock-in' : 'stock-out' ?>">
                                    <span class="stock-indicator"></span>
                                    <?php echo $data[$i][0]['item_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                                </div>
                                <div class="item-price">Ksh <?php echo number_format($data[$i][0]['item_price']) ?></div>
                                <div class="quantity-controls">
                                    <a href="javascript:void(0)" onclick="updateQuantity(<?php echo $data[$i][0]['item_id'] ?>, 'decrease')" class="quantity-btn">
                                        <i class="fas fa-minus"></i>
                                    </a>
                                    <div class="quantity-display">
                                        <?php echo $_SESSION['cart'][$i]['quantity'] ?>
                                    </div>
                                    <a href="javascript:void(0)" onclick="updateQuantity(<?php echo $data[$i][0]['item_id'] ?>, 'increase')" class="quantity-btn">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                                <a href="cart.php?delete=<?php echo $data[$i][0]['item_id'] ?>" class="delete-btn">
                                    <i class="fas fa-trash"></i>
                                    Remove Item
                                </a>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>

            <div class="cart-summary">
                <div class="summary-header">
                    <span class="summary-icon">
                        <i class="fas fa-calculator"></i>
                    </span>
                    <h2 class="summary-title">Order Summary</h2>
                </div>
                <div class="total-amount">
                    <div class="total-items">
                        <?php echo $num . " " . ($num == 1 ? "item" : "items"); ?> in your cart
                    </div>
                    <div class="total-price">
                        Ksh <?php echo number_format(total_price($data)); ?>
                    </div>
                </div>
                <div class="action-buttons">
                    <a href="cart.php?delete_all=1" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        Clear Cart
                    </a>
                    <a href="receipt.php?order=done" class="btn btn-success">
                        <i class="fas fa-credit-card"></i>
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h1>Your cart is empty</h1>
                <p>Looks like you haven't added any items to your cart yet. Start shopping to fill it up!</p>
                <a href="index.php" class="shop-now-btn">
                    <i class="fas fa-shopping-bag"></i>
                    Start Shopping
                </a>
            </div>
        <?php
        }
        ?>
    </div>

    <?php include "includes/footer.php" ?>

    <!-- Snackbar for notifications -->
    <div id="snackbar" class="snackbar">
        <span id="snackbar-message"></span>
        <button id="snackbar-close" onclick="closeSnackbar()">&times;</button>
    </div>

    <script>
    function showSnackbar(message, type = 'error') {
        const snackbar = document.getElementById('snackbar');
        const messageElement = document.getElementById('snackbar-message');
        
        messageElement.textContent = message;
        snackbar.className = 'snackbar show ' + type;
        
        setTimeout(() => {
            snackbar.className = snackbar.className.replace('show', '');
        }, 3000);
    }

    function closeSnackbar() {
        const snackbar = document.getElementById('snackbar');
        snackbar.className = snackbar.className.replace('show', '');
    }

    function updateQuantity(itemId, action) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'cart_ajax.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update quantity display
                    const quantityDisplay = document.querySelector(`[data-item-id="${itemId}"] .quantity-display`);
                    if (quantityDisplay) {
                        quantityDisplay.textContent = response.newQuantity;
                    }
                    
                    // Update total price
                    const totalPriceElement = document.querySelector('.total-price');
                    if (totalPriceElement) {
                        totalPriceElement.textContent = 'Ksh ' + response.totalPrice.toLocaleString();
                    }
                    
                    // Update item count
                    const totalItemsElement = document.querySelector('.total-items');
                    if (totalItemsElement) {
                        const itemText = response.totalItems == 1 ? 'item' : 'items';
                        totalItemsElement.textContent = response.totalItems + ' ' + itemText + ' in your cart';
                    }
                    
                    // If quantity becomes 0, remove the item
                    if (response.newQuantity === 0) {
                        const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
                        if (cartItem) {
                            cartItem.style.transition = 'all 0.3s ease';
                            cartItem.style.opacity = '0';
                            cartItem.style.transform = 'translateX(-100%)';
                            setTimeout(() => {
                                cartItem.remove();
                                // Check if cart is empty
                                if (response.totalItems === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    }
                } else {
                    // Show error message in snackbar
                    showSnackbar(response.message, 'error');
                }
            }
        };
        
        xhr.send('action=' + action + '&item_id=' + itemId);
    }
    </script>
</body>
</html>