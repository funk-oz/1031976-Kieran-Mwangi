<?php
//session_start();

// Include enhanced error handling and validation
require_once __DIR__ . '/../admin/includes/validation.php';
require_once __DIR__ . '/../admin/includes/error_handler.php';

// Enhanced database connection with error handling
try {
    $connection = mysqli_connect("localhost", "root", "", "PharmEasy");
    if (!$connection) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to prevent SQL injection
    mysqli_set_charset($connection, "utf8mb4");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    logSecurityEvent("Database Connection Failed", $e->getMessage());
    die("Database connection failed. Please try again later.");
}
    function post_redirect($url)
{
    ob_start();
    header('Location: ' . $url);
    // header('Location: https://md-taha-ahmed.000webhostapp.com/pharmeasy/' . $url);
    ob_end_flush();
    die();
}

function get_redirect($url)
{
    echo " <script> 
    window.location.href = '" . $url . "'; 
    </script>";
    // echo "<script>
    // window.location.href = 'https://md-taha-ahmed.000webhostapp.com/pharmeasy/" . $url . "';
    // </script>";
}
function query($query)
{
    global $connection;
    $run = mysqli_query($connection, $query);
    if ($run) {
        while ($row = $run->fetch_assoc()) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        } else {
            return "";
        }
    } else {
        return 0;
    }
}
function single_query($query)
{
    global $connection;
    if (mysqli_query($connection, $query)) {
        return "done";
    } else {
        die("no data" . mysqli_connect_error($connection));
    }
}
function login()
{
    if (isset($_POST['login'])) {

        $userEmail = trim(strtolower($_POST['userEmail']));
        $password = trim($_POST['password']);
        if (empty($userEmail) or empty($password)) {
            $_SESSION['message'] = "empty_err";
            post_redirect("login.php");
        }
        $query = "SELECT  email , user_id , user_password FROM user WHERE email= '$userEmail' ";
        $data = query($query);
        if (empty($data)) {
            $_SESSION['message'] = "loginErr";
            post_redirect("login.php");
        } elseif (password_verify($password, $data[0]['user_password']) and  $userEmail == $data[0]['email']) {
            $_SESSION['user_id'] = $data[0]['user_id'];
            post_redirect("index.php");
        } else {
            $_SESSION['message'] = "loginErr";
            post_redirect("login.php");
        }
    }
}

function singUp()
{
    if (isset($_POST['singUp'])) {
        // Check rate limiting for signup attempts
        if (!checkRateLimit('user_signup', 3, 3600)) {
            $_SESSION['message'] = "Too many signup attempts. Please try again in 1 hour.";
            logSecurityEvent("User Signup Rate Limit Exceeded", "IP: " . $_SERVER['REMOTE_ADDR']);
            post_redirect("signUp.php");
            return;
        }
        
        // Validate CSRF token if present
        if (isset($_POST['csrf_token']) && !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['message'] = "Invalid security token. Please refresh the page and try again.";
            logSecurityEvent("CSRF Token Validation Failed", "User signup attempt");
            post_redirect("signUp.php");
            return;
        }
        
        // Enhanced input validation using Validation class
        $email = Validation::validateEmail($_POST['email'], 'Email');
        $fname = Validation::validateText($_POST['Fname'], 'First Name', 1, 50);
        $lname = Validation::validateText($_POST['Lname'], 'Last Name', 1, 50);
        $address = Validation::validateText($_POST['address'], 'Address', 5, 255);
        $phone = Validation::validatePhone($_POST['phone'], 'Phone');
        $gender = Validation::validateGender($_POST['gender'], 'Gender');
        $passwd = Validation::validatePassword($_POST['passwd'], 'Password');
        
        // Check for validation errors
        if (Validation::hasErrors()) {
            $_SESSION['message'] = Validation::getFirstError();
            post_redirect("signUp.php");
            return;
        }
        
        // Check if email already exists using prepared statement
        global $connection;
        $check_query = "SELECT email FROM user WHERE email = ?";
        
        try {
            $stmt = mysqli_prepare($connection, $check_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existing_user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if ($existing_user) {
                $_SESSION['message'] = "This email is already registered. Please use a different email or try logging in.";
                logSecurityEvent("Duplicate Signup Attempt", "Email: $email");
                post_redirect("signUp.php");
                return;
            }
            
            // Hash the password
            $hashed_password = password_hash($passwd, PASSWORD_DEFAULT);
            
            // Insert new user using prepared statement
            $insert_query = "INSERT INTO user (email, user_fname, user_Lname, user_address, user_phone, user_gender, user_password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $insert_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 'sssssss', $email, $fname, $lname, $address, $phone, $gender, $hashed_password);
            $success = mysqli_stmt_execute($stmt);
            $user_id = mysqli_insert_id($connection);
            mysqli_stmt_close($stmt);
            
            if ($success && $user_id) {
                // Set session and redirect
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_fname'] = $fname;
                
                // Log successful signup
                logSecurityEvent("User Signup Successful", "User ID: $user_id, Email: $email");
                
                post_redirect("index.php");
            } else {
                $_SESSION['message'] = "Registration failed. Please try again.";
                logSecurityEvent("User Signup Failed", "Database insert failed for email: $email");
                post_redirect("signUp.php");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("User Signup Exception", $e->getMessage());
            $_SESSION['message'] = "An error occurred during registration. Please try again.";
            post_redirect("signUp.php");
        }
    }
}
function message()
{
    if (isset($_SESSION['message'])) {
        if ($_SESSION['message'] == "signup_err_password") {
            echo "   <div class='alert alert-danger' role='alert'>
        please enter the password in correct form !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "loginErr") {
            echo "   <div class='alert alert-danger' role='alert'>
        The email or the password is incorrect !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "usedEmail") {
            echo "   <div class='alert alert-danger' role='alert'>
        This email is already used !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "wentWrong") {
            echo "   <div class='alert alert-danger' role='alert'>
        Something went wrong !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "empty_err") {
            echo "   <div class='alert alert-danger' role='alert'>
        please don't leave anything empty !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "signup_err_email") {
            echo "   <div class='alert alert-danger' role='alert'>
        please enter the email in the correct form !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "signup_err_phone") {
            echo "   <div class='alert alert-danger' role='alert'>
        please enter the phone number in the correct form !!!
      </div>";
            unset($_SESSION['message']);
        } elseif ($_SESSION['message'] == "signup_err_gender") {
            echo "   <div class='alert alert-danger' role='alert'>
        please select a valid gender !!!
      </div>";
            unset($_SESSION['message']);
        }
    }
    
    // Handle stock validation messages
    if (isset($_SESSION['stock_message'])) {
        if ($_SESSION['stock_message'] == "out_of_stock") {
            echo "<div class='alert alert-danger stock-alert' role='alert'>
                <i class='fas fa-exclamation-triangle'></i>
                This product is currently out of stock and cannot be added to your cart.
            </div>";
            unset($_SESSION['stock_message']);
        } elseif ($_SESSION['stock_message'] == "insufficient_stock") {
            $available = isset($_SESSION['available_stock']) ? $_SESSION['available_stock'] : 0;
            $in_cart = isset($_SESSION['current_cart_quantity']) ? $_SESSION['current_cart_quantity'] : 0;
            echo "<div class='alert alert-warning stock-alert' role='alert'>
                <i class='fas fa-info-circle'></i>
                Insufficient stock! Only $available units available. You already have $in_cart in your cart.
            </div>";
            unset($_SESSION['stock_message']);
            unset($_SESSION['available_stock']);
            unset($_SESSION['current_cart_quantity']);
        } elseif ($_SESSION['stock_message'] == "added_to_cart") {
            echo "<div class='alert alert-success stock-alert' role='alert'>
                <i class='fas fa-check-circle'></i>
                Product successfully added to your cart!
            </div>";
            unset($_SESSION['stock_message']);
        }
    }
    
    // Handle order validation errors
    if (isset($_SESSION['order_error'])) {
        echo "<div class='alert alert-danger stock-alert' role='alert'>
            <i class='fas fa-exclamation-triangle'></i>
            <strong>Order Failed:</strong><br>" . nl2br(htmlspecialchars($_SESSION['order_error'])) . "
        </div>";
        unset($_SESSION['order_error']);
    }
}
function search()
{
    if (isset($_GET['search'])) {
        $search_text = $_GET['search_text'];
        if ($search_text == "") {
            return;
        }
        $query = "SELECT i.*, c.category_name FROM item i LEFT JOIN categories c ON i.category_id = c.category_id WHERE i.item_tags LIKE '%$search_text%' AND c.category_status = 1 AND (i.item_status = 1 OR i.item_status IS NULL)";
        $data = query($query);
        return $data;
    } elseif (isset($_GET['cat'])) {
        $cat = $_GET['cat'];
        $query = "SELECT i.*, c.category_name FROM item i LEFT JOIN categories c ON i.category_id = c.category_id WHERE c.category_name='$cat' AND c.category_status = 1 AND (i.item_status = 1 OR i.item_status IS NULL) ORDER BY RAND()";
        $data = query($query);
        return $data;
    }
}
function all_products()
{
    $query = "SELECT i.*, c.category_name FROM item i LEFT JOIN categories c ON i.category_id = c.category_id WHERE c.category_status = 1 AND (i.item_status = 1 OR i.item_status IS NULL) ORDER BY RAND()";
    $data = query($query);
    return $data;
}
function total_price($data)
{
    $sum = 0;
    $num = sizeof($data);
    for ($i = 0; $i < $num; $i++) {
        // Corrected the line below to accumulate the correct sum
        $sum += ($data[$i][0]['item_price'] * $_SESSION['cart'][$i]['quantity']);
    }
    return $sum;
}
function get_item()
{
    if (isset($_GET['product_id'])) {
        $_SESSION['item_id'] = $_GET['product_id'];
        $id = $_GET['product_id'];
        $query = "SELECT i.*, c.category_name FROM item i LEFT JOIN categories c ON i.category_id = c.category_id WHERE i.item_id='$id'";
        $data = query($query);
        return $data;
    }
}function add_cart($item_id)
{
    $user_id = $_SESSION['user_id'];
    $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1; // Default quantity to 1 if not set
    
    // Validate quantity is positive
    if ($quantity <= 0) {
        $_SESSION['stock_message'] = "Quantity must be a positive number.";
        get_redirect("product.php?product_id=" . $item_id);
        return;
    }

    if (empty($user_id)) {
        get_redirect("login.php");
        return;
    }

    // Get current item stock
    $item_data = get_item_id($item_id);
    if (empty($item_data)) {
        $_SESSION['stock_message'] = "Product not found.";
        if (isset($_GET['cart'])) {
            get_redirect("product.php?product_id=" . $item_id);
        } elseif (isset($_GET['buy'])) {
            get_redirect("product.php?product_id=" . $item_id);
        }
        return;
    }

    $available_stock = $item_data[0]['item_quantity'];
    
    // Check if product is out of stock
    if ($available_stock <= 0) {
        $_SESSION['stock_message'] = "out_of_stock";
        if (isset($_GET['cart'])) {
            get_redirect("product.php?product_id=" . $item_id);
        } elseif (isset($_GET['buy'])) {
            get_redirect("product.php?product_id=" . $item_id);
        }
        return;
    }

    // Calculate current quantity in cart for this item
    $current_cart_quantity = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $cart_item) {
            if ($cart_item['item_id'] == $item_id) {
                $current_cart_quantity = $cart_item['quantity'];
                break;
            }
        }
    }

    // Check if requested quantity exceeds available stock
    $total_requested = $current_cart_quantity + $quantity;
    if ($total_requested > $available_stock) {
        $_SESSION['stock_message'] = "insufficient_stock";
        $_SESSION['available_stock'] = $available_stock;
        $_SESSION['current_cart_quantity'] = $current_cart_quantity;
        if (isset($_GET['cart'])) {
            get_redirect("product.php?product_id=" . $item_id);
        } elseif (isset($_GET['buy'])) {
            get_redirect("product.php?product_id=" . $item_id);
        }
        return;
    }

    // Add to cart if stock validation passes
    if (isset($_SESSION['cart'])) {
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['item_id'] == $item_id) {
                $cart_item['quantity'] += $quantity;
                $item_exists = true;
                break;
            }
        }
        if (!$item_exists) {
            $_SESSION['cart'][] = array('user_id' => $user_id, 'item_id' => $item_id, 'quantity' => $quantity);
        }
    } else {
        $_SESSION['cart'] = array(array('user_id' => $user_id, 'item_id' => $item_id, 'quantity' => $quantity));
    }

    $_SESSION['stock_message'] = "added_to_cart";
    
    if (isset($_GET['cart'])) {
        get_redirect("product.php?product_id=" . $item_id);
    } elseif (isset($_GET['buy'])) {
        get_redirect("cart.php");
    }
}

function get_cart()
{
    $num = sizeof($_SESSION['cart']);
    if (isset($num)) {
        for ($i = 0; $i < $num; $i++) {
            $item_id = $_SESSION['cart'][$i]['item_id'];
            $query = "SELECT item_id, item_image ,item_title  ,item_quantity ,item_price ,item_brand FROM item WHERE item_id='$item_id'";
            $data[$i] = query($query);
        }
        return $data;
    }
}
function delete_from_cart()
{
    if (isset($_GET['delete'])) {
        $item_id = $_GET['delete'];
        $num = sizeof($_SESSION['cart']);
        for ($i = 0; $i < $num; $i++) {
            if ($_SESSION['cart'][$i]['item_id'] == $item_id) {
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
        get_redirect("cart.php");
    } elseif (isset($_GET['delete_all'])) {
        unset($_SESSION['cart']);
        get_redirect("cart.php");
    } elseif (isset($_GET['increase'])) {
        $item_id = $_GET['increase'];
        $num = sizeof($_SESSION['cart']);
        for ($i = 0; $i < $num; $i++) {
            if ($_SESSION['cart'][$i]['item_id'] == $item_id) {
                $_SESSION['cart'][$i]['quantity']++;
                break;
            }
        }
        get_redirect("cart.php");
    } elseif (isset($_GET['decrease'])) {
        $item_id = $_GET['decrease'];
        $num = sizeof($_SESSION['cart']);
        for ($i = 0; $i < $num; $i++) {
            if ($_SESSION['cart'][$i]['item_id'] == $item_id) {
                if ($_SESSION['cart'][$i]['quantity'] > 1) {
                    $_SESSION['cart'][$i]['quantity']--;
                } else {
                    // If quantity becomes 0, remove item from cart
                    unset($_SESSION['cart'][$i]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                }
                break;
            }
        }
        get_redirect("cart.php");
    }
}
function add_order()
{
    if (isset($_GET['order'])) {
        $num = sizeof($_SESSION['cart']);
        date_default_timezone_set("Asia/Kolkata");
        $date = date("Y-m-d");
        
        // First pass: Validate all items have sufficient stock
        $validation_errors = [];
        for ($i = 0; $i < $num; $i++) {
            $item_id = $_SESSION['cart'][$i]['item_id'];
            $quantity = $_SESSION['cart'][$i]['quantity'];
            
            // Validate quantity is positive
            if (!is_numeric($quantity) || $quantity <= 0) {
                $validation_errors[] = "Invalid quantity detected. Quantity must be a positive number.";
                continue;
            }
            
            $item = get_item_id($item_id);
            if (empty($item)) {
                $validation_errors[] = "Product not found (ID: $item_id)";
                continue;
            }
            
            $available_stock = $item[0]['item_quantity'];
            $item_title = $item[0]['item_title'];
            
            if ($available_stock < $quantity) {
                $validation_errors[] = "$item_title: Only $available_stock units available, but $quantity requested";
            }
        }
        
        // If there are validation errors, redirect back to cart with error message
        if (!empty($validation_errors)) {
            $_SESSION['order_error'] = "Order cannot be processed due to insufficient stock:\n" . implode("\n", $validation_errors);
            get_redirect("cart.php");
            return;
        }
        
        // Second pass: Process the order only if all validations pass
        for ($i = 0; $i < $num; $i++) {
            $item_id = $_SESSION['cart'][$i]['item_id'];
            $user_id = $_SESSION['cart'][$i]['user_id'];
            $quantity = $_SESSION['cart'][$i]['quantity'];
            
            // Additional validation for quantity
            if (!is_numeric($quantity) || $quantity <= 0) {
                continue; // Skip items with invalid quantity
            }
            
            // Insert order record
            $query = "INSERT INTO orders (user_id,item_id,order_quantity,order_date) 
                    VALUES('$user_id','$item_id','$quantity','$date')";
            $data = single_query($query);
            
            // Update stock quantity
            $item = get_item_id($item_id);
            $new_quantity = $item[0]['item_quantity'] - $quantity;
            
            // Ensure stock doesn't go below 0 (additional safety check)
            $new_quantity = max(0, $new_quantity);
            
            $query = "UPDATE item SET item_quantity='$new_quantity' WHERE item_id = '$item_id'";
            single_query($query);
        }
        
        unset($_SESSION['cart']);
        get_redirect("final.php");
    }
}
function check_user($id)
{
    $query = "SELECT user_id FROM user where user_id='$id'";
    $row = query($query);
    if (empty($row)) {
        return 0;
    } else {
        return 1;
    }
}
function get_user($id)
{
    $query = "SELECT user_id ,user_fname ,user_lname ,email ,user_address FROM user WHERE user_id=$id";
    $data = query($query);
    return $data;
}
function get_item_id($id)
{
    $query = "SELECT * FROM item WHERE item_id= '$id'";
    $data = query($query);
    return $data;
}

function get_active_categories()
{
    $query = "SELECT * FROM categories WHERE category_status = 1 ORDER BY category_name";
    $data = query($query);
    return $data;
}

// Password Reset Functions
function store_password_reset($email, $token, $expires) {
    global $connection;
    
    // Clear any existing reset tokens for this user
    $clear_query = "UPDATE user SET reset_token = NULL, reset_expires = NULL WHERE email = ?";
    $stmt = mysqli_prepare($connection, $clear_query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Store new reset token
    $update_query = "UPDATE user SET reset_token = ?, reset_expires = ? WHERE email = ?";
    $stmt = mysqli_prepare($connection, $update_query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'sss', $token, $expires, $email);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function get_password_reset($token) {
    global $connection;
    
    $query = "SELECT user_id, email, reset_expires FROM user WHERE reset_token = ? AND reset_expires > NOW()";
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $data;
}

function update_user_password($email, $new_password) {
    global $connection;
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password and clear reset token
    $query = "UPDATE user SET user_password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?";
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'ss', $hashed_password, $email);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function clear_expired_resets() {
    global $connection;
    
    // Clear expired reset tokens
    $query = "UPDATE user SET reset_token = NULL, reset_expires = NULL WHERE reset_expires < NOW()";
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        return false;
    }
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result;
}

function redirect($url)
{
    post_redirect($url);
}

function add_category()
{
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $status = isset($_POST['status']) ? $_POST['status'] : 1;
        
        // Handle image upload
        $image_name = '';
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $target_dir = "../images/";
            $image_name = basename($_FILES['category_image']['name']);
            $target_file = $target_dir . $image_name;
            
            // Check if image file is valid
            $check = getimagesize($_FILES['category_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
                    // Image uploaded successfully
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your image.";
                    return;
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                return;
            }
        }
        
        $query = "INSERT INTO categories (category_name, category_description, category_image, category_status) VALUES ('$name', '$description', '$image_name', '$status')";
        $result = query($query);
        
        if ($result) {
            $_SESSION['message'] = "Category added successfully!";
        } else {
            $_SESSION['message'] = "Failed to add category.";
        }
        redirect("categories.php");
    }
}

function edit_category($id = null)
{
    if (isset($_POST['edit_category'])) {
        $id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        
        // Handle image upload
        $image_update = '';
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
            $target_dir = "../images/";
            $image_name = basename($_FILES['category_image']['name']);
            $target_file = $target_dir . $image_name;
            
            // Check if image file is valid
            $check = getimagesize($_FILES['category_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
                    $image_update = ", category_image = '$image_name'";
                } else {
                    $_SESSION['message'] = "Sorry, there was an error uploading your image.";
                    return;
                }
            } else {
                $_SESSION['message'] = "File is not an image.";
                return;
            }
        }
        
        $query = "UPDATE categories SET category_name = '$name', category_description = '$description', category_status = '$status' $image_update WHERE category_id = '$id'";
        $result = single_query($query);
        
        if ($result) {
            $_SESSION['message'] = "Category updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to update category.";
        }
        redirect("categories.php");
    }
}

function get_category_by_id($id)
{
    $query = "SELECT * FROM categories WHERE category_id = '$id'";
    $data = query($query);
    return $data;
}

// Auto-cleanup expired resets when this function is called
function cleanup_expired_resets() {
    // Only cleanup once per session to avoid excessive database calls
    if (!isset($_SESSION['cleanup_done'])) {
        clear_expired_resets();
        $_SESSION['cleanup_done'] = true;
    }
}

// Check if email exists in user table
function check_email_user($email) {
    global $connection;
    
    $query = "SELECT email FROM user WHERE email = ?";
    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $data ? true : false;
}

function get_products_by_category($category_id)
{
    $query = "SELECT * FROM item WHERE category_id = '$category_id' ORDER BY item_title";
    $data = query($query);
    return $data;
}

function show_message()
{
    message();
}

function all_categories()
{
    $query = "SELECT * FROM categories ORDER BY category_name";
    $data = query($query);
    return $data;
}

function get_category_product_count($category_id)
{
    $query = "SELECT COUNT(*) as count FROM item WHERE category_id = '$category_id'";
    $data = query($query);
    return $data ? $data[0]['count'] : 0;
}

function delete_category()
{
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $query = "DELETE FROM categories WHERE category_id = '$id'";
        $result = single_query($query);
        if ($result) {
            $_SESSION['message'] = "Category deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete category.";
        }
        redirect("categories.php");
    }
}

function get_category($id)
{
    $query = "SELECT * FROM categories WHERE category_id = '$id'";
    $data = query($query);
    return $data;
}

function get_user_data($user_id) {
    global $connection;
    
    $user_id = mysqli_real_escape_string($connection, $user_id);
    $query = "SELECT user_fname, user_Lname, email FROM user WHERE user_id = '$user_id'";
    $result = mysqli_query($connection, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

function get_cart_count($user_id = null) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    // Count unique items, not total quantity
    return count($_SESSION['cart']);
}
