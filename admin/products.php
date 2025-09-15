<?php include "includes/head.php"; ?>
<style>
    /* General Styles */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .container {
        margin-top: 20px;
        margin-left: 0%;
    }

    /* Sidebar styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100px;
        height: 100%;
        background-color: #f8f9fa;
        padding: 15px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .main-content {
        margin-left: 250px; /* Width of the sidebar */
        padding: 20px;
    }

    /* Header styles */
    header {
        position: sticky;
            top: 0;
            z-index: 1030;
            background-color:  #198754;
            color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
        
    }

    /* Forms */
    form {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .form-control::placeholder {
        color: #6c757d;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        text-decoration: none;
        color: #198754;
    }

    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
        background-color: transparent;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
        background-color: transparent;
    }

    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
        background-color: transparent;
    }

    .btn-outline-primary:hover {
        color: #fff;
        background-color: #007bff;
    }

    .btn-outline-danger:hover {
        color: #fff;
        background-color: #dc3545;
    }

    .btn-outline-warning:hover {
        color: #fff;
        background-color: #ffc107;
    }

    /* Table */
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
    }

    .table thead {
        background-color: #f1f1f1;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<body>
    <?php include "includes/header.php"; ?>

    <div class="sidebar">
        <?php include "includes/sidebar.php"; ?>
    </div>

    <div class="main-content">
        <?php
        // Process product edit on POST before any output so redirects work
        edit_item();
        // Now it's safe to print messages
        message();
        ?>

        <div class="container">
            <div class="row align-items-start">
                <div class="col">
                    <h2>Products details</h2>
                </div>
                <div class="col">
                    <!-- Empty Column -->
                </div>
                <div class="col">
                    <form class="d-flex" method="GET" action="products.php">
                        <?php if (isset($_GET['category']) && !empty($_GET['category'])): ?>
                            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">
                        <?php endif; ?>
                        <input class="form-control" type="search" name="search_item_name" placeholder="Search for product" aria-label="Search">
                        <button class="btn btn-outline-secondary" type="submit" name="search_item" value="search">Search</button>
                    </form>
                </div>
            </div>

            <?php
            if (isset($_GET['edit'])) {
                $_SESSION['id'] = $_GET['edit'];
                $data = get_item($_SESSION['id']);
            ?>
                <h2>Edit Product Details</h2>
                <form action="products.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($data[0]['item_id']); ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($data[0]['item_image']); ?>">
                    <?php if (isset($_GET['category']) && !empty($_GET['category'])): ?>
                        <input type="hidden" name="category_id" value="<?php echo $_GET['category']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($data[0]['category_id']); ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Product name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($data[0]['item_title']); ?>" name="name" required>
                        <div class="form-text">Please enter the product name in range (1-25) characters, special characters not allowed!</div>
                    </div>
                    <div class="form-group">
                        <label>Brand name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($data[0]['item_brand']); ?>" name="brand" required>
                        <div class="form-text">Please enter the brand name in range (1-25) characters, special characters not allowed!</div>
                    </div>
                    <label>Category</label>
                        <select name="cat" class="form-control" required>
                            <?php 
                            $categories = all_categories();
                            foreach($categories as $category) {
                                $selected = ($data[0]['category_id'] == $category['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product tags</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($data[0]['item_tags']); ?>" name="tags" required>
                        <div class="form-text">Please enter tags for the product in range (1-250) characters, special characters not allowed!</div>
                    </div>
                    <div class="form-group">
                        <label>Product image</label>
                        <input type="file" accept="image/*" class="form-control" name="image">
                        <div class="form-text">Current image: <?php echo $data[0]['item_image']; ?></div>
                    </div>
                    <div class="form-group">
                        <label>Product quantity</label>
                        <input type="number" class="form-control" value="<?php echo htmlspecialchars($data[0]['item_quantity']); ?>" name="quantity" min="1" max="999" required onchange="validateQuantity(this)">
                        <div class="form-text">Please enter the quantity of the product in range (1-999).</div>
                    </div>
                    <div class="form-group">
                        <label>Product price</label>
                        <div class="input-group">
                            <span class="input-group-text">Ksh</span>
                            <input type="number" step="0.01" class="form-control" aria-label="Amount" name="price" value="<?php echo htmlspecialchars($data[0]['item_price']); ?>" min="0.01" required onchange="validatePrice(this)">
                            <span class="input-group-text">.00</span>
                        </div>
                        <div class="form-text">Please enter the price of the product.</div>
                    </div>
                    <div class="form-group">
                        <label>Product details</label>
                        <textarea class="form-control" name="details" rows="3" required><?php echo htmlspecialchars($data[0]['item_details']); ?></textarea>
                        <div class="form-text">Please enter the product details.</div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary" value="update" name="update">Update Product</button>
                    <button type="submit" class="btn btn-outline-danger" value="cancel" name="cancel">Cancel</button>
                </form>
                
            <?php } ?>

            <?php add_item(); if (isset($_GET['add'])) { 
                $category_id = isset($_GET['category']) ? $_GET['category'] : '';
            ?>
                <h2>Add Product</h2>
                <form action="products.php" method="POST" enctype="multipart/form-data">
                    <?php if (isset($_GET['category']) && !empty($_GET['category'])): ?>
                        <input type="hidden" name="category_id" value="<?php echo $_GET['category']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Product name</label>
                        <input type="text" class="form-control" placeholder="Product name" name="name" required>
                        <div class="form-text">Please enter the product name in range (1-25) characters, special characters not allowed!</div>
                    </div>
                    <div class="form-group">
                        <label>Brand name</label>
                        <input type="text" class="form-control" placeholder="Product brand" name="brand" required>
                        <div class="form-text">Please enter the brand name in range (1-25) characters, special characters not allowed!</div>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="cat" class="form-control" required>
                            <?php 
                            $categories = all_categories();
                            foreach($categories as $category) {
                                $selected = ($category_id == $category['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product tags</label>
                        <input type="text" class="form-control" placeholder="Product tags" name="tags" required>
                        <div class="form-text">Please enter tags for the product in range (1-250) characters, special characters not allowed!</div>
                    </div>
                    <div class="form-group">
                        <label>Product image</label>
                        <input type="file" accept="image/*" class="form-control" name="image" required>
                        <div class="form-text">Please upload an image for the product.</div>
                    </div>
                    <div class="form-group">
                        <label>Product quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" max="999" required onchange="validateQuantity(this)">
                        <div class="form-text">Please enter the quantity of the product in range (1-999).</div>
                    </div>
                    <div class="form-group">
                        <label>Product price</label>
                        <div class="input-group">
                            <span class="input-group-text">Ksh</span>
                            <input type="number" step="0.01" class="form-control" aria-label="Amount" name="price" min="0.01" required onchange="validatePrice(this)">
                            <span class="input-group-text">.00</span>
                        </div>
                        <div class="form-text">Please enter the price of the product.</div>
                    </div>
                    <div class="form-group">
                        <label>Product details</label>
                        <textarea class="form-control" name="details" rows="3" required></textarea>
                        <div class="form-text">Please enter the product details.</div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary" value="update" name="add_item">Add Product</button>
                    <button type="submit" class="btn btn-outline-danger" value="cancel" name="cancel">Cancel</button>
                </form>
            <?php } ?>

            <?php if (isset($_GET['category']) && !empty($_GET['category'])): 
                $cat_data = get_category($_GET['category']);
                if (!empty($cat_data)):
            ?>
                <div class="alert alert-info" role="alert">
                    <strong>Viewing products in category:</strong> <?php echo htmlspecialchars($cat_data[0]['category_name']); ?>
                    <a href="categories.php?id=<?php echo $_GET['category']; ?>" class="btn btn-sm btn-outline-primary float-end">← Back to Category</a>
                </div>
            <?php endif; endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Tags</th>
                            <th>Image</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Details</th>
                            <th>
                                <button type="button" class="btn btn-outline-primary"><a href="products.php?add=1<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>" style="text-decoration: none; color: black;">Add</a></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get items filtered by category if specified
                        if (isset($_GET['category']) && !empty($_GET['category'])) {
                            $data = all_items(intval($_GET['category']));
                        } else {
                            $data = all_items();
                        }
                        delete_item();
                        if (isset($_GET['search_item'])) {
                            $query = search_item();
                            if (isset($query)) {
                                $data = $query;
                            } else {
                                get_redirect("products.php");
                            }
                        } elseif (isset($_GET['id'])) {
                            $data = get_item_details();
                        }
                        if (isset($data)) {
                            $num = sizeof($data);
                            for ($i = 0; $i < $num; $i++) {
                                // Get category name
                                $category_name = 'Unknown';
                                if (!empty($data[$i]['category_id'])) {
                                    $cat_data = get_category($data[$i]['category_id']);
                                    if (!empty($cat_data)) {
                                        $category_name = $cat_data[0]['category_name'];
                                    }
                                }
                        ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo $data[$i]['item_id']; ?></td>
                                <td><?php echo $data[$i]['item_title']; ?></td>
                                <td><?php echo $data[$i]['item_brand']; ?></td>
                                <td><?php echo $category_name; ?></td>
                                <td><?php echo $data[$i]['item_tags']; ?></td>
                                <td><img src="../images/<?php echo $data[$i]['item_image']; ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                                <td><?php echo $data[$i]['item_quantity']; ?></td>
                                <td>Ksh <?php echo $data[$i]['item_price']; ?></td>
                                <td><?php echo strlen($data[$i]['item_details']) > 30 ? substr($data[$i]['item_details'], 0, 30) . '...' : $data[$i]['item_details']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-outline-warning"><a href="products.php?edit=<?php echo $data[$i]['item_id']; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>" style="text-decoration: none; color: black;">Edit</a></button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger"><a href="products.php?delete=<?php echo $data[$i]['item_id']; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?>" style="text-decoration: none; color: black;" onclick="return confirm('Delete this product?')">Delete</a></button>
                                </td>
                            </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
    
    <script>
        // Client-side validation for numeric fields
        function validateQuantity(input) {
            const value = parseFloat(input.value);
            if (value <= 0) {
                alert('Quantity must be a positive number greater than 0.');
                input.value = 1;
                input.focus();
                return false;
            }
            if (value > 999) {
                alert('Quantity cannot exceed 999.');
                input.value = 999;
                input.focus();
                return false;
            }
            if (Math.floor(value) !== value) {
                alert('Quantity must be a whole number.');
                input.value = Math.floor(value);
                input.focus();
                return false;
            }
            return true;
        }
        
        function validatePrice(input) {
            const value = parseFloat(input.value);
            if (value <= 0) {
                alert('Price must be a positive number greater than 0.');
                input.value = 0.01;
                input.focus();
                return false;
            }
            return true;
        }
        
        // Form submission validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const quantityInputs = form.querySelectorAll('input[name="quantity"]');
                    const priceInputs = form.querySelectorAll('input[name="price"]');
                    
                    // Validate quantity fields
                    quantityInputs.forEach(input => {
                        if (!validateQuantity(input)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                    
                    // Validate price fields
                    priceInputs.forEach(input => {
                        if (!validatePrice(input)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                });
            });
        });
    </script>
</body>