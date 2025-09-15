<?php
include "includes/head.php";
require_once 'includes/functions.php';
?>

<body>
  <?php
  $data = get_item();
  if (isset($_GET['cart']) || isset($_GET['buy'])) {
    add_cart($_SESSION['item_id']);
  }
  ?>
  <br>

  <?php message(); // Display stock validation messages ?>

  <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .product-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-image {
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .product-image img {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
        }
        .product-details {
            flex: 2;
            min-width: 300px;
        }
        .product-purchase {
            flex: 1;
            min-width: 200px;
        }
        .product-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .product-info {
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 15px 0;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            flex: 1;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .purchase-box {
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
        }
        .price {
            color: #d9534f;
            margin-bottom: 10px;
        }
        .stock {
            color: #198754;
            margin-bottom: 10px;
        }
        .out-of-stock {
            color: #dc3545;
            font-weight: bold;
        }
        .delivery-info {
            color: #007bff;
            margin-bottom: 15px;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ffc107;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            margin-bottom: 10px;
            text-align: center;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #e0a800;
        }
    </style>
  <div class="container">
        <div class="product-layout">
            <div class="product-image">
                <img src="images/<?php echo $data[0]['item_image'] ?>" alt="<?php echo $data[0]['item_title'] ?>">
            </div>
            
            <div class="product-details">
                <h4 class="product-title"><?php echo $data[0]['item_title'] ?></h4>
                
                <div class="product-info">
                    <div class="info-row">
                        <div class="info-label">Brand         :</div>
                        <div class="info-value"><?php echo $data[0]['item_brand'] ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Category:</div>
                        <div class="info-value">
                            <?php
                            $category_name = '';
                            if (isset($data[0]['category_name']) && $data[0]['category_name'] !== '') {
                                $category_name = $data[0]['category_name'];
                            } elseif (isset($data[0]['category_id'])) {
                                $catData = get_category_by_id($data[0]['category_id']);
                                if (!empty($catData) && isset($catData[0]['category_name'])) {
                                    $category_name = $catData[0]['category_name'];
                                }
                            }
                            echo htmlspecialchars($category_name ?: 'Uncategorized');
                            ?>
                        </div>
                    </div>
                </div>

                <h5 class="product-title">About this item:</h5>
                <p><?php echo $data[0]['item_details'] ?></p>
            </div>
            
            <div class="product-purchase">
                <div class="purchase-box">
                    <h5 class="price">Ksh<?php echo $data[0]['item_price'] ?></h5>
                    <?php if ((int)$data[0]['item_quantity'] > 0) { ?>
                        <h6 class="stock" style="color:#198754;">In Stock</h6>
                    <?php } else { ?>
                        <small class="out-of-stock" style="color:#dc3545;">Out Of Stock</small>
                    <?php } ?>
                    <p class="delivery-info" style="color: black">
                        Deliver to: 
                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $user = get_user($_SESSION['user_id']);
                            echo $user[0]['user_address'];
                        } else {
                            echo "Btm (Store)";
                        }
                        ?>
                    </p>
                    <?php if ((int)$data[0]['item_quantity'] > 0) { ?>
                        <form action="product.php" method="GET" onsubmit="return validateProductQuantity()">
                            <input id="productQty" value="1" type="number" name="quantity" min="1" max="<?php echo (int)$data[0]['item_quantity']; ?>" onchange="validateQuantityInput(this)">
                            <button type="submit" class="btn" value="buy" name="buy">Buy Now</button>
                            <button type="submit" class="btn" value="" name="cart">Add to Cart</button>
                        </form>
                        <script>
                        (function() {
                            var available = <?php echo (int)$data[0]['item_quantity']; ?>;
                            var form = document.querySelector('form[action="product.php"]');
                            var input = document.getElementById('productQty');
                            if (!form || !input) return;

                            function clamp() {
                                var v = parseInt(input.value || '1', 10);
                                if (isNaN(v) || v < 1) v = 1;
                                if (v > available) v = available;
                                input.value = v;
                            }

                            function findBtn(selectors) {
                                for (var i = 0; i < selectors.length; i++) {
                                    var el = form.querySelector(selectors[i]);
                                    if (el) return el;
                                }
                                return null;
                            }

                            var incBtn = findBtn(['[data-action="increase"]', '.increase', '.qty-plus', '.quantity-plus', '.qty_increase']);
                            var decBtn = findBtn(['[data-action="decrease"]', '.decrease', '.qty-minus', '.quantity-minus', '.qty_decrease']);

                            function updateState() {
                                var v = parseInt(input.value || '1', 10);
                                if (decBtn) {
                                    decBtn.disabled = v <= 1;
                                    if (decBtn.classList) decBtn.classList.toggle('disabled', v <= 1);
                                }
                                if (incBtn) {
                                    incBtn.disabled = v >= available;
                                    if (incBtn.classList) incBtn.classList.toggle('disabled', v >= available);
                                }
                            }

                            if (incBtn) {
                                incBtn.addEventListener('click', function(e) {
                                    clamp();
                                    var v = parseInt(input.value, 10);
                                    if (v >= available) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                    }
                                }, true);
                            }
                            if (decBtn) {
                                decBtn.addEventListener('click', function(e) {
                                    clamp();
                                    var v = parseInt(input.value, 10);
                                    if (v <= 1) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                    }
                                }, true);
                            }

                            input.addEventListener('input', function() { clamp(); updateState(); });

                            // init
                            input.setAttribute('max', available);
                            input.setAttribute('min', '1');
                            clamp();
                            updateState();
                        })();
                        
                        // Additional validation functions
                        function validateQuantityInput(input) {
                            const value = parseInt(input.value);
                            const max = parseInt(input.getAttribute('max'));
                            const min = parseInt(input.getAttribute('min'));
                            
                            if (value < min) {
                                input.value = min;
                            } else if (value > max) {
                                input.value = max;
                            }
                            
                            if (value <= 0) {
                                input.value = 1;
                            }
                        }
                        
                        function validateProductQuantity() {
                            const input = document.getElementById('productQty');
                            const value = parseInt(input.value);
                            
                            if (value <= 0) {
                                alert('Quantity must be a positive number greater than 0.');
                                input.value = 1;
                                input.focus();
                                return false;
                            }
                            
                            if (value > <?php echo (int)$data[0]['item_quantity']; ?>) {
                                alert('Quantity cannot exceed available stock.');
                                input.value = <?php echo (int)$data[0]['item_quantity']; ?>;
                                input.focus();
                                return false;
                            }
                            
                            return true;
                        }
                        </script>
                    <?php } else { ?>
                        <div class="delivery-info" style="color:#6c757d;">Currently unavailable for purchase</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>