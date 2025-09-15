<?php
include "includes/head.php";

// Get dashboard statistics
$total_categories = query("SELECT COUNT(*) as count FROM categories")[0]['count'];
$active_categories = query("SELECT COUNT(*) as count FROM categories WHERE category_status = 1")[0]['count'];
$inactive_categories = $total_categories - $active_categories;
$total_products = query("SELECT COUNT(*) as count FROM item")[0]['count'];
$total_customers = query("SELECT COUNT(*) as count FROM user")[0]['count'];
$total_orders = query("SELECT COUNT(*) as count FROM orders")[0]['count'];
$pending_orders = query("SELECT COUNT(*) as count FROM orders WHERE order_status = 0")[0]['count'];
$completed_orders = query("SELECT COUNT(*) as count FROM orders WHERE order_status = 1")[0]['count'];

// Get recent activity
$recent_orders = query("SELECT o.order_id, o.order_date, o.order_quantity, i.item_title, u.user_fname, u.user_lname 
                      FROM orders o 
                      JOIN item i ON o.item_id = i.item_id 
                      JOIN user u ON o.user_id = u.user_id 
                      ORDER BY o.order_date DESC LIMIT 5");

// Get top selling products
$top_products = query("SELECT i.item_title, SUM(o.order_quantity) as total_sold 
                      FROM orders o 
                      JOIN item i ON o.item_id = i.item_id 
                      GROUP BY o.item_id 
                      ORDER BY total_sold DESC LIMIT 5");
?>

<style>
/* Modern Dashboard Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
}

.container-fluid {
    background: transparent;
}

.main-content {
    margin-left: 250px;
    padding: 30px;
    background: transparent;
}

.dashboard-header {
    margin-bottom: 40px;
    text-align: center;
}

.dashboard-header p {
    color: #333;
    font-size: 1.3rem;
    margin: 0;
    font-weight: 500;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.stat-card .icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #198754, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-card h3 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.stat-card .number {
    font-size: 48px;
    font-weight: bold;
    color: #198754;
    margin-bottom: 10px;
    line-height: 1;
}

.stat-card .label {
    color: #666;
    font-size: 14px;
}

.dashboard-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

.section-card {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
}

.section-card h2 {
    margin: 0 0 25px 0;
    color: #333;
    font-size: 24px;
    font-weight: 600;
}

.recent-item {
    display: flex;
    align-items: center;
    padding: 15px;
    margin-bottom: 10px;
    background: rgba(25, 135, 84, 0.1);
    border-radius: 12px;
    border-left: 4px solid #198754;
}

.recent-item:last-child {
    margin-bottom: 0;
}

.recent-item .info {
    flex: 1;
}

.recent-item .title {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.recent-item .subtitle {
    color: #666;
    font-size: 14px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.action-btn {
    display: block;
    padding: 20px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    text-decoration: none;
    color: #333;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
    font-weight: 600;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    color: #198754;
}

.action-btn .icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 15px;
    background: linear-gradient(135deg, #198754, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

/* Responsive Design */
@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .dashboard-sections {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .dashboard-header p {
        font-size: 1.2rem;
    }
    
    .stat-card .number {
        font-size: 36px;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<body>
    <?php include "includes/header.php"; ?>
    
    <div class="container-fluid">
        <?php include "includes/sidebar.php"; ?>
        
        <main class="main-content">
            <?php message(); ?>
            
            <div class="dashboard-header">
                <p>Welcome back! Here's your pharmacy management overview</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">📊</div>
                    <h3>Total Categories</h3>
                    <div class="number"><?php echo $total_categories; ?></div>
                    <div class="label"><?php echo $active_categories; ?> active, <?php echo $inactive_categories; ?> inactive</div>
                </div>
                
                <div class="stat-card">
                    <div class="icon">📦</div>
                    <h3>Total Products</h3>
                    <div class="number"><?php echo $total_products; ?></div>
                    <div class="label">Products in inventory</div>
                </div>
                
                <div class="stat-card">
                    <div class="icon">👥</div>
                    <h3>Total Customers</h3>
                    <div class="number"><?php echo $total_customers; ?></div>
                    <div class="label">Registered customers</div>
                </div>
                
                <div class="stat-card">
                    <div class="icon">🛒</div>
                    <h3>Total Orders</h3>
                    <div class="number"><?php echo $total_orders; ?></div>
                    <div class="label"><?php echo $pending_orders; ?> pending, <?php echo $completed_orders; ?> completed</div>
                </div>
            </div>
            
            <div class="dashboard-sections">
                <div class="section-card">
                    <h2>Recent Sales</h2>
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="recent-item">
                                <div class="info">
                                    <div class="title">Order #<?php echo $order['order_id']; ?></div>
                                    <div class="subtitle">
                                        <?php echo $order['user_fname'] . ' ' . $order['user_lname']; ?> - 
                                        <?php echo $order['order_quantity']; ?>x <?php echo $order['item_title']; ?>
                                        <br><small><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No recent orders found</p>
                    <?php endif; ?>
                </div>
                
                <div class="section-card">
                    <h2>Top Selling Products</h2>
                    <?php if (!empty($top_products)): ?>
                        <?php foreach ($top_products as $product): ?>
                            <div class="recent-item">
                                <div class="info">
                                    <div class="title"><?php echo $product['item_title']; ?></div>
                                    <div class="subtitle"><?php echo $product['total_sold']; ?> units sold</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No sales data available</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="section-card">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <a href="categories.php" class="action-btn">
                        <div class="icon">📂</div>
                        Manage Categories
                    </a>
                    <a href="products.php" class="action-btn">
                        <div class="icon">📦</div>
                        Manage Products
                    </a>
                    <a href="customers.php" class="action-btn">
                        <div class="icon">👥</div>
                        View Customers
                    </a>
                    <a href="orders.php" class="action-btn">
                        <div class="icon">🛒</div>
                        View Orders
                    </a>
                    <a href="admin.php" class="action-btn">
                        <div class="icon">⚙️</div>
                        Admin Management
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <?php include "includes/footer.php"; ?>
</body>
</html>