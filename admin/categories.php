<?php 
include "includes/head.php"; 

add_category();
edit_category();
delete_product();
?>

<style>
    /* General Styles */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    /* Alert Styles */
    .alert {
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    /* Header and Footer */
    header, footer {
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: #198754;
        color: #fff;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        background-color: #f8f9fa;
        padding: 15px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    /* Main Content */
    .main-content {
        margin-left: 250px; /* Width of the sidebar */
        padding: 20px;
    }

    /* Container */
    .container {
        margin-top: 20px;
    }

    /* Form Styles */
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
        margin: 2px;
    }

    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-success {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-warning {
        color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-danger {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-info {
        color: #fff;
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Table */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
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

    /* Cards */
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: -0.5rem;
    }

    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0.5rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .text-center {
        text-align: center;
    }

    .text-success {
        color: #28a745;
    }

    .text-muted {
        color: #6c757d;
    }

    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .badge-success {
        color: #fff;
        background-color: #28a745;
    }

    .badge-secondary {
        color: #fff;
        background-color: #6c757d;
    }
</style>

<body>
    <?php include "includes/header.php"; ?>
    <?php include "includes/sidebar.php"; ?>

    <main class="main-content">
        <?php message(); ?>

        <div class="container">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Categories & Products</h1>
                <a href="?add=true" class="btn btn-primary">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Category
                </a>
            </div>

            <?php if (isset($_GET['view_category'])): 
                $category_id = $_GET['view_category'];
                $category = get_category($category_id);
                $products = get_products_by_category($category_id);
            ?>
                <!-- Single Category View with Products -->
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($category[0]['category_name']); ?> - Products</h3>
                        <div>
                            <a href="products.php?add=1&category=<?php echo $category_id; ?>" class="btn btn-success btn-sm">Add Product</a>
                            <a href="categories.php" class="btn btn-secondary btn-sm">Back to Categories</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($products)): ?>
                            <div class="row">
                                <?php foreach ($products as $product): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <img src="../images/<?php echo $product['item_image']; ?>" style="width: 100%; height: 150px; object-fit: cover;" alt="<?php echo htmlspecialchars($product['item_title']); ?>">
                                            <div class="card-body">
                                                <h6><?php echo strlen($product['item_title']) > 20 ? substr($product['item_title'], 0, 20) . "..." : $product['item_title']; ?></h6>
                                                <p class="text-success">Ksh<?php echo $product['item_price']; ?></p>
                                                <p class="small">Stock: <?php echo $product['item_quantity']; ?></p>
                                                <div>
                                                    <a href="products.php?edit=<?php echo $product['item_id']; ?>&category=<?php echo $category_id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="?delete_product=<?php echo $product['item_id']; ?>&category=<?php echo $category_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No products in this category yet. <a href="products.php?add=1&category=<?php echo $category_id; ?>">Add the first product</a></p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif (isset($_GET['add'])): ?>
                <!-- Add Category Form -->
                <div class="card">
                    <div class="card-header">
                        <h3>Add New Category</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Category Description</label>
                                <textarea class="form-control" rows="3" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Category Image</label>
                                <input type="file" name="category_image" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>

            <?php elseif (isset($_GET['edit'])): 
                $_SESSION['id'] = $_GET['edit'];
                $data = get_category($_SESSION['id']);
            ?>
                <!-- Edit Category Form -->
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Category</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($data[0]['category_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Category Description</label>
                                <textarea class="form-control" rows="3" name="description"><?php echo htmlspecialchars($data[0]['category_description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Category Image</label>
                                <input type="file" name="category_image" class="form-control" accept="image/*">
                                <?php if ($data[0]['category_image']): ?>
                                    <small>Current image: <?php echo $data[0]['category_image']; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?php echo ($data[0]['category_status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($data[0]['category_status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <input type="hidden" name="category_id" value="<?php echo $data[0]['category_id']; ?>">
                            <button type="submit" class="btn btn-primary" name="edit_category">Update Category</button>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Categories List -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $categories = all_categories();
                            delete_category();
                            if (!empty($categories)):
                                foreach ($categories as $index => $category): 
                                    $product_count = get_category_product_count($category['category_id']);
                            ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                    <td><?php echo strlen($category['category_description']) > 50 ? substr($category['category_description'], 0, 50) . '...' : $category['category_description']; ?></td>
                                    <td>
                                        <?php if ($category['category_image']): ?>
                                            <img src="../images/<?php echo $category['category_image']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            No image
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo ($category['category_status'] == 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>'; ?></td>
                                    <td><?php echo $product_count; ?> products</td>
                                    <td>
                                        <a href="?view_category=<?php echo $category['category_id']; ?>" class="btn btn-info btn-sm">View Products</a>
                                        <a href="?edit=<?php echo $category['category_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete=<?php echo $category['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete category and all its products?')">Delete</a>
                                    </td>
                                </tr>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center">No categories found. <a href="?add=true">Create your first category</a></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
