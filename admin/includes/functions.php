<?php
// Include enhanced error handling and validation
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/error_handler.php';
require_once __DIR__ . '/config.php';

// Enhanced database connection with error handling
try {
$connection = mysqli_connect("localhost", "root", "", "PharmEasy");
    if (!$connection) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to prevent SQL injection
    mysqli_set_charset($connection, "utf8mb4");
    
    // Ensure admin_password column can hold password hashes
    try {
        $checkCol = mysqli_query($connection, "SHOW COLUMNS FROM admin LIKE 'admin_password'");
        if ($checkCol && mysqli_num_rows($checkCol) === 1) {
            $col = mysqli_fetch_assoc($checkCol);
            if (isset($col['Type']) && preg_match('/varchar\((\d+)\)/i', $col['Type'], $m)) {
                if ((int)$m[1] < 60) {
                    @mysqli_query($connection, "ALTER TABLE admin MODIFY admin_password VARCHAR(255) NOT NULL");
                }
            }
        }
    } catch (Exception $e) {
        // ignore migration issues
    }

    // Ensure admin_role column exists and set roles
    try {
        $roleCol = mysqli_query($connection, "SHOW COLUMNS FROM admin LIKE 'admin_role'");
        if ($roleCol && mysqli_num_rows($roleCol) === 0) {
            @mysqli_query($connection, "ALTER TABLE admin ADD COLUMN admin_role ENUM('super_admin','pharmacist') NOT NULL DEFAULT 'pharmacist'");
        }
        // Seed super_admin role for allow-listed emails
        if (defined('SUPER_ADMIN_EMAILS')) {
            $allow = json_decode(SUPER_ADMIN_EMAILS, true);
            if (is_array($allow) && !empty($allow)) {
                $placeholders = implode(",", array_fill(0, count($allow), "?"));
                $sql = "UPDATE admin SET admin_role='super_admin' WHERE LOWER(admin_email) IN ($placeholders)";
                $stmt = mysqli_prepare($connection, $sql);
                if ($stmt) {
                    // bind dynamic params
                    $types = str_repeat('s', count($allow));
                    $lowered = array_map(function($e){ return strtolower($e); }, $allow);
                    mysqli_stmt_bind_param($stmt, $types, ...$lowered);
                    @mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    } catch (Exception $e) {
        // ignore migration issues
    }
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    logSecurityEvent("Database Connection Failed", $e->getMessage());
    die("Database connection failed. Please try again later.");
}
// query functions (start)
function query($query)
{
    global $connection;
    
    // Validate query parameter
    if (empty($query) || !is_string($query)) {
        logSecurityEvent("Invalid Query Parameter", "Empty or non-string query provided");
        return array();
    }
    
    // Sanitize query for logging (remove sensitive data)
    $log_query = preg_replace('/password\s*=\s*[\'"]?[^\'"]*[\'"]?/i', 'password=***', $query);
    $log_query = preg_replace('/admin_password\s*=\s*[\'"]?[^\'"]*[\'"]?/i', 'admin_password=***', $log_query);
    
    try {
        $data = array();
    $run = mysqli_query($connection, $query);
        
    if ($run) {
        while ($row = $run->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
            $error = handleDatabaseError($connection, $query);
            if ($error) {
                logSecurityEvent("Query Execution Failed", $error);
            }
            return array();
        }
    } catch (Exception $e) {
        logSecurityEvent("Query Exception", $e->getMessage());
        error_log("Query exception: " . $e->getMessage());
        return array();
    }
}
function single_query($query)
{
    global $connection;
    
    // Validate query parameter
    if (empty($query) || !is_string($query)) {
        logSecurityEvent("Invalid Single Query Parameter", "Empty or non-string query provided");
        return 0;
    }
    
    try {
    $run = mysqli_query($connection, $query);
    if ($run) {
        return 1;
    } else {
            $error = handleDatabaseError($connection, $query);
            if ($error) {
                logSecurityEvent("Single Query Execution Failed", $error);
            }
            return 0;
        }
    } catch (Exception $e) {
        logSecurityEvent("Single Query Exception", $e->getMessage());
        error_log("Single query exception: " . $e->getMessage());
        return 0;
    }
}
// query functions (end)
// redirect functions (start)
function post_redirect($url)
{
    ob_start();
    header('Location: ' . $url);
    // header('Location: https://md-taha-ahmed.000webhostapp.com/pharmeasy/admin/' . $url);
    ob_end_flush();
    die();
}
function get_redirect($url)
{
    echo " <script> 
    window.location.href = '$url'; 
    </script>";
    // echo "<script>
    // window.location.href = 'https://md-taha-ahmed.000webhostapp.com/pharmeasy/admin/" . $url . "';
    // </script>";
}
// redirect functions (end)
// messages function (start)
function message()
{
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        
        // Error messages
        if ($msg == "loginErr") {
            echo "<div class='alert alert-danger' role='alert'>There is no account with this email !!!</div>";
        } elseif ($msg == "emailErr") {
            echo "<div class='alert alert-danger' role='alert'>The email address is already taken. Please choose another</div>";
        } elseif ($msg == "loginErr1") {
            echo "<div class='alert alert-danger' role='alert'>The email or password is wrong!</div>";
        } elseif ($msg == "noResult") {
            echo "<div class='alert alert-danger' role='alert'>There is no user with this email address.</div>";
        } elseif ($msg == "itemErr") {
            echo "<div class='alert alert-danger' role='alert'>There is a product with the same name.</div>";
        } elseif ($msg == "noResultOrder") {
            echo "<div class='alert alert-danger' role='alert'>There is no order with this ID !!!</div>";
        } elseif ($msg == "noResultItem") {
            echo "<div class='alert alert-danger' role='alert'>There is no product with this name !!!</div>";
        } elseif ($msg == "noResultAdmin") {
            echo "<div class='alert alert-danger' role='alert'>There is no admin with this email !!!</div>";
        } elseif ($msg == "empty_err") {
            echo "<div class='alert alert-danger' role='alert'>Please don't leave anything empty !!!</div>";
        } elseif (strpos($msg, "Image upload failed") !== false || strpos($msg, "File is not an image") !== false || strpos($msg, "error uploading") !== false) {
            echo "<div class='alert alert-danger' role='alert'>$msg</div>";
        } elseif (strpos($msg, "Failed to") !== false) {
            echo "<div class='alert alert-danger' role='alert'>$msg</div>";
        }
        // Success messages
        elseif (strpos($msg, "successfully") !== false || strpos($msg, "added") !== false || strpos($msg, "updated") !== false || strpos($msg, "deleted") !== false) {
            echo "<div class='alert alert-success' role='alert'>$msg</div>";
        }
        // Default error for unrecognized messages
        else {
            echo "<div class='alert alert-info' role='alert'>$msg</div>";
        }
        
        unset($_SESSION['message']);
    }
}
// messages function (end)
// login function (start)
function login()
{
    if(isset($_POST['login'])) {
        // Check rate limiting for login attempts
        if (!checkRateLimit('admin_login', 5, 900)) {
            $_SESSION['message'] = "Too many login attempts. Please try again in 15 minutes.";
            logSecurityEvent("Admin Login Rate Limit Exceeded", "IP: " . $_SERVER['REMOTE_ADDR']);
            return;
        }
        
        // Validate CSRF token if present
        if (isset($_POST['csrf_token']) && !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['message'] = "Invalid security token. Please refresh the page and try again.";
            logSecurityEvent("CSRF Token Validation Failed", "Admin login attempt");
            return;
        }
        
        $email = trim(strtolower($_POST['adminEmail']));
        $password = trim($_POST['adminPassword']);

        // Enhanced input validation
        if(empty($email) || empty($password)) {
            $_SESSION['message'] = "Please enter both email and password.";
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Please enter a valid email address.";
            return;
        }
        
        // Sanitize inputs
        global $connection;
        $email = sanitizeSQLInput($email, $connection);
        
        $query = "SELECT admin_id, admin_email, admin_password FROM admin WHERE admin_email=?";
        
        try {
            $stmt = mysqli_prepare($connection, $query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);

        if ($data && count($data) > 0) {
            $admin = $data[0];

            $stored = $admin['admin_password'];
            $loginOk = false;

            // Support both hashed and legacy plain-text passwords
            if (password_get_info($stored)['algo']) {
                // stored is a hash
                $loginOk = password_verify($password, $stored);
            } else {
                // legacy plain text compare
                if ($password === $stored) {
                    $loginOk = true;
                    // upgrade to hash after successful legacy login
                    try {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $upgradeStmt = mysqli_prepare($connection, "UPDATE admin SET admin_password=? WHERE admin_id=?");
                        if ($upgradeStmt) {
                            mysqli_stmt_bind_param($upgradeStmt, 'si', $newHash, $admin['admin_id']);
                            mysqli_stmt_execute($upgradeStmt);
                            mysqli_stmt_close($upgradeStmt);
                        }
                    } catch (Exception $e) {
                        // ignore upgrade failure, continue login
                    }
                }
            }

            if ($loginOk) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_email'] = $admin['admin_email'];
                // Load role if available
                try {
                    $roleQ = mysqli_query($connection, "SHOW COLUMNS FROM admin LIKE 'admin_role'");
                    if ($roleQ && mysqli_num_rows($roleQ) === 1) {
                        $rid = intval($admin['admin_id']);
                        $rres = mysqli_query($connection, "SELECT admin_role FROM admin WHERE admin_id=$rid");
                        if ($rres && $r = mysqli_fetch_assoc($rres)) {
                            $_SESSION['admin_role'] = $r['admin_role'];
                        }
                    } else {
                        // fallback based on email allow-list
                        $_SESSION['admin_role'] = (defined('SUPER_ADMIN_EMAILS') && is_super_admin_email($admin['admin_email'])) ? 'super_admin' : 'pharmacist';
                    }
                } catch (Exception $e) {
                    $_SESSION['admin_role'] = (defined('SUPER_ADMIN_EMAILS') && is_super_admin_email($admin['admin_email'])) ? 'super_admin' : 'pharmacist';
                }
                $_SESSION['admin_login_time'] = time();

                logSecurityEvent("Admin Login Successful", "Admin ID: " . $admin['admin_id']);
                safeRedirect("index.php", "Login successful!");
                exit();
            }
        }

        // If we reach here, login failed
        $_SESSION['message'] = "The email or password is incorrect.";
            logSecurityEvent("Admin Login Failed", "Invalid credentials for email: $email");
            
        } catch (Exception $e) {
            logSecurityEvent("Admin Login Exception", $e->getMessage());
            $_SESSION['message'] = "An error occurred during login. Please try again.";
        }
    }
}
// login function (end)
// user functions (start)
function all_users()
{
    $query = "SELECT user_id ,user_fname ,user_lname ,email ,user_address ,user_phone ,user_gender FROM user";
    $data = query($query);
    return $data;
}
function delete_user()
{
    if (isset($_GET['delete'])) {
        // Validate CSRF token if present
        if (isset($_GET['csrf_token']) && !validateCSRFToken($_GET['csrf_token'])) {
            $_SESSION['message'] = "Invalid security token. Please refresh the page and try again.";
            logSecurityEvent("CSRF Token Validation Failed", "User delete attempt");
            safeRedirect("customers.php");
            return;
        }
        
        $userId = intval($_GET['delete']);
        
        // Validate user ID
        if ($userId <= 0) {
            $_SESSION['message'] = "Invalid user ID.";
            logSecurityEvent("Invalid User ID", "Delete attempt with ID: " . $_GET['delete']);
            safeRedirect("customers.php");
            return;
        }
        
        // Check if user exists before deletion
        global $connection;
        $check_query = "SELECT user_id, user_fname, user_lname FROM user WHERE user_id = ?";
        
        try {
            $stmt = mysqli_prepare($connection, $check_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if (!$user) {
                $_SESSION['message'] = "User not found.";
                logSecurityEvent("User Delete Failed", "User ID not found: $userId");
                safeRedirect("customers.php");
                return;
            }
            
            // Delete user
            $delete_query = "DELETE FROM user WHERE user_id = ?";
            $stmt = mysqli_prepare($connection, $delete_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 'i', $userId);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                $_SESSION['message'] = "User '{$user['user_fname']} {$user['user_lname']}' deleted successfully!";
                logSecurityEvent("User Deleted", "User ID: $userId, Name: {$user['user_fname']} {$user['user_lname']}");
            } else {
                $_SESSION['message'] = "Failed to delete user.";
                logSecurityEvent("User Delete Failed", "User ID: $userId");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("User Delete Exception", $e->getMessage());
            $_SESSION['message'] = "An error occurred while deleting user.";
        }
        
        safeRedirect("customers.php");
        
    } elseif (isset($_GET['undo'])) {
        $userId = intval($_GET['undo']);
        $query = "INSERT INTO user (user_fname, user_lname, email, user_address) VALUES ('', '', '', '')";
        $run = single_query($query);
        if ($run) {
            $_SESSION['message'] = "User restored successfully!";
            logSecurityEvent("User Restored", "User ID: $userId");
        } else {
            $_SESSION['message'] = "Failed to restore user.";
            logSecurityEvent("User Restore Failed", "User ID: $userId");
        }
        safeRedirect("customers.php");
    }
}
function edit_user($id)
{
    if (isset($_POST['update'])) {
        // Validate CSRF token if present
        if (isset($_POST['csrf_token']) && !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['message'] = "Invalid security token. Please refresh the page and try again.";
            logSecurityEvent("CSRF Token Validation Failed", "User edit attempt");
            safeRedirect("customers.php");
            return;
        }
        
        // Validate and sanitize inputs
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
        $address = trim($_POST['address']);
        
        // Basic validation
        if (empty($fname)) {
            $_SESSION['message'] = "First name is required.";
            safeRedirect("customers.php");
            return;
        }
        
        if (empty($lname)) {
            $_SESSION['message'] = "Last name is required.";
            safeRedirect("customers.php");
            return;
        }
        
        if (empty($email)) {
            $_SESSION['message'] = "Email is required.";
            safeRedirect("customers.php");
            return;
        }
        
        if (empty($address)) {
            $_SESSION['message'] = "Address is required.";
            safeRedirect("customers.php");
            return;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Invalid email format.";
            safeRedirect("customers.php");
            return;
        }
        
        // Validate phone if provided (phone is optional)
        if (!empty($phone)) {
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($clean_phone) < 10 || strlen($clean_phone) > 15) {
                $_SESSION['message'] = "Phone number must be between 10 and 15 digits.";
                safeRedirect("customers.php");
                return;
            }
        }
        
        // Validate gender if provided (gender is optional)
        if (!empty($gender) && !in_array($gender, ['Male', 'Female', 'Other'])) {
            $_SESSION['message'] = "Please select a valid gender.";
            safeRedirect("customers.php");
            return;
        }
        
        // Check if email is already used by another user
        global $connection;
        $sanitized_email = sanitizeSQLInput($email, $connection);
        $sanitized_id = intval($id);
        
        $check_query = "SELECT user_id FROM user WHERE email = ? AND user_id != ?";
        
        try {
            $stmt = mysqli_prepare($connection, $check_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 'si', $sanitized_email, $sanitized_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existing_user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if ($existing_user) {
                $_SESSION['message'] = "This email is already used by another user.";
                safeRedirect("customers.php");
                return;
            }
            
            // Update user
            $update_query = "UPDATE user SET email = ?, user_fname = ?, user_lname = ?, user_phone = ?, user_gender = ?, user_address = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($connection, $update_query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, 'ssssssi', $sanitized_email, $fname, $lname, $phone, $gender, $address, $sanitized_id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            if ($success) {
                $_SESSION['message'] = "User updated successfully!";
                logSecurityEvent("User Updated", "User ID: $sanitized_id");
        } else {
                $_SESSION['message'] = "Failed to update user.";
                logSecurityEvent("User Update Failed", "User ID: $sanitized_id");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("User Edit Exception", $e->getMessage());
            $_SESSION['message'] = "An error occurred while updating user.";
        }
        
        safeRedirect("customers.php");
        
    } elseif (isset($_POST['cancel'])) {
        safeRedirect("customers.php");
    }
}
function get_user($id)
{
    $query = "SELECT user_id ,user_fname ,user_lname ,email ,user_address ,user_phone ,user_gender FROM user WHERE user_id=$id";
    $data = query($query);
    return $data;
}
function check_email_user($email)
{
    $query = "SELECT email FROM user WHERE email='$email'";
    $data = query($query);
    if ($data) {
        return 1;
    } else {
        return 0;
    }
}
function search_user()
{
    if (isset($_GET['search_user'])) {
        $email = trim(strtolower($_GET['search_user_email']));
        if (empty($email)) {
            return;
        }
        $query = "SELECT user_id ,user_fname ,user_lname ,email ,user_address ,user_phone ,user_gender FROM user WHERE email='$email'";
        $data = query($query);
        if ($data) {
            return $data;
        } else {
            $_SESSION['message'] = "noResult";
            return;
        }
    }
}
function get_user_details()
{
    if ($_GET['id']) {
        $id = $_GET['id'];
        $query = "SELECT * FROM user WHERE user_id=$id";
        $data = query($query);
        return $data;
    }
}
// user functions (end)
// item functions (start)
function all_items($category_id = null)
{
    if ($category_id !== null) {
        $query = "SELECT * FROM item WHERE category_id = $category_id";
    } else {
        $query = "SELECT * FROM item";
    }
    $data = query($query);
    return $data;
}
function delete_item()
{
    if (isset($_GET['delete'])) {
        $itemID = $_GET['delete'];
        $query = "DELETE FROM item WHERE item_id ='$itemID'";
        $run = single_query($query);
        
        // Redirect back to the specific category if we came from there
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            get_redirect("products.php?category=" . $_GET['category']);
        } else {
            get_redirect("products.php");
        }
    }
}
function get_item($id)
{
    $query = "SELECT * FROM item WHERE item_id=$id";
    $data = query($query);
    return $data;
}
function check_name($name)
{
    $query = "SELECT item_title FROM item WHERE item_title='$name'";
    $data = query($query);
    return !empty($data) ? 1 : 0;
}

function search_item()
{
    if (isset($_GET['search_item'])) {
        $name = trim($_GET['search_item_name']);
        
        // Build query with optional category filter
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $category_id = intval($_GET['category']);
            $query = "SELECT * FROM item WHERE item_title LIKE '%$name%' AND category_id = $category_id";
        } else {
            $query = "SELECT * FROM item WHERE item_title LIKE '%$name%'";
        }
        
        $data = query($query);
        if ($data) {
            return $data;
        } else {
            $_SESSION['message'] = "noResultItem";
            return;
        }
    }
}
function get_item_details()
{
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM item WHERE item_id=$id";
        $data = query($query);
        return $data;
    }
}

// item functions (end)
// admin functions (start)
function all_admins()
{
    $query = "SELECT admin_id ,admin_fname ,admin_lname ,admin_email  FROM admin";
    $data = query($query);
    return $data;
}
function get_admin($id)
{
    $query = "SELECT admin_id ,admin_fname ,admin_lname ,admin_email  FROM admin WHERE admin_id=$id";
    $data = query($query);
    return $data;
}
function edit_admin($id)
{
    if (isset($_POST['admin_update'])) {
        // Only super admin can edit admin records
        if (!current_user_is_super_admin()) {
            $_SESSION['message'] = "You do not have permission to edit admins.";
            get_redirect("admin.php");
            return;
        }
        $fname = trim($_POST['admin_fname']);
        $lname = trim($_POST['admin_lname']);
        $email = trim(strtolower($_POST['admin_email']));
        $password = trim($_POST['admin_password']);
        $check = check_email_admin($email);
        if ($check == 0) {
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE admin SET admin_email='$email' ,admin_fname='$fname' ,admin_lname='$lname' ,admin_password='$hashed'  WHERE admin_id= '$id'";
            } else {
                $query = "UPDATE admin SET admin_email='$email' ,admin_fname='$fname' ,admin_lname='$lname'  WHERE admin_id= '$id'";
            }
            single_query($query);
            get_redirect("admin.php");
        } else {
            $_SESSION['message'] = "emailErr";
            get_redirect("admin.php");
        }
    } elseif (isset($_POST['admin_cancel'])) {
        get_redirect("admin.php");
    }
}
function check_email_admin($email)
{
    $query = "SELECT admin_email FROM admin WHERE admin_email='$email'";
    $data = query($query);
    if ($data) {
        return $data;
    } else {
        return 0;
    }
}
function is_super_admin_email($email)
{
    $allow = json_decode(SUPER_ADMIN_EMAILS, true);
    $email = strtolower(trim($email));
    return in_array($email, array_map('strtolower', $allow));
}

function current_user_is_super_admin()
{
    if (!isset($_SESSION['admin_email'])) return false;
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin') return true;
    return is_super_admin_email($_SESSION['admin_email']);
}

function add_admin()
{
    if (isset($_POST['add_admin'])) {
        if (!current_user_is_super_admin()) {
            $_SESSION['message'] = "You do not have permission to add admins.";
            get_redirect("admin.php");
            return;
        }
        $fname = trim($_POST['admin_fname']);
        $lname = trim($_POST['admin_lname']);
        $email = trim(strtolower($_POST['admin_email']));
        $password = trim($_POST['admin_password']);
        $check = check_email_admin($email);
        if ($check == 0) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            // role column might not exist in some environments; try to insert with role, fallback without
            $query = "INSERT INTO admin (admin_fname, admin_lname, admin_email, admin_password, admin_role) VALUES ('$fname','$lname','$email','$hashed','pharmacist')";
            $ok = single_query($query);
            if (!$ok) {
                $query = "INSERT INTO admin (admin_fname, admin_lname, admin_email, admin_password) VALUES ('$fname','$lname','$email','$hashed')";
                $ok = single_query($query);
            }
            get_redirect("admin.php");
        } else {
            $_SESSION['message'] = "emailErr";
            get_redirect("admin.php");
        }
    } elseif (isset($_POST['admin_cancel'])) {
        get_redirect("admin.php");
    }
}
function delete_admin()
{
    if (isset($_GET['delete'])) {
        // Only super admin can delete, and cannot delete super admins
        if (!current_user_is_super_admin()) {
            $_SESSION['message'] = "You do not have permission to delete admins.";
            get_redirect("admin.php");
            return;
        }
        $adminId = $_GET['delete'];
        // Prevent deleting super admin rows (by allow-list)
        $info = query("SELECT admin_email FROM admin WHERE admin_id='" . mysqli_real_escape_string($GLOBALS['connection'], $adminId) . "'");
        if (!empty($info)) {
            $email = $info[0]['admin_email'];
            if (is_super_admin_email($email)) {
                $_SESSION['message'] = "Cannot delete a super admin.";
                get_redirect("admin.php");
                return;
            }
        }
        $query = "DELETE FROM admin WHERE admin_id ='$adminId'";
        $run = single_query($query);
        get_redirect("admin.php");
    }
}
function search_admin()
{
    if (isset($_GET['search_admin'])) {
        $email = trim(strtolower($_GET['search_admin_email']));
        if (empty($email)) {
            return;
        }
        $query = "SELECT admin_id ,admin_fname ,admin_lname ,admin_email FROM admin WHERE admin_email='$email'";
        $data = query($query);
        if ($data) {
            return $data;
        } else {
            $_SESSION['message'] = "noResultAdmin";
            return;
        }
    }
}
function check_admin($id)
{
    $query = "SELECT admin_id FROM admin where admin_id='$id'";
    $row = query($query);
    if (empty($row)) {
        return 0;
    } else {
        return 1;
    }
}
function all_categories()
{
    $query = "SELECT * FROM categories ORDER BY category_name";
    $data = query($query);
    return $data;
}

function get_category($id)
{
    $query = "SELECT * FROM categories WHERE category_id = '$id'";
    $data = query($query);
    return $data;
}

function get_products_by_category($category_id)
{
    $query = "SELECT * FROM item WHERE category_id = '$category_id' ORDER BY item_title";
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
        post_redirect("categories.php");
    }
}

function add_category()
{
    if (isset($_POST['add_category'])) {
        $name = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['name']));
        $description = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['description']));
        $status = isset($_POST['status']) ? $_POST['status'] : 1;
        
        // Validation
        if (empty($name)) {
            $_SESSION['message'] = "Category name is required.";
            post_redirect("categories.php");
            return;
        }
        
        // Handle image upload (now optional)
        $image_name = '';
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['category_image'], 'category_');
            if ($upload_result['success']) {
                $image_name = $upload_result['filename'];
            } else {
                // Log the error but continue without image
                $_SESSION['message'] = "Category created successfully, but image upload failed: " . $upload_result['error'];
            }
        }
        
        $query = "INSERT INTO categories (category_name, category_description, category_image, category_status) VALUES ('$name', '$description', '$image_name', '$status')";
        $result = single_query($query);
        
        if ($result) {
            if (empty($_SESSION['message'])) {
                $_SESSION['message'] = "Category added successfully!";
            }
        } else {
            $_SESSION['message'] = "Failed to add category. Error: " . mysqli_error($GLOBALS['connection']);
        }
        post_redirect("categories.php");
    }
}

function edit_category()
{
    if (isset($_POST['edit_category'])) {
        $id = mysqli_real_escape_string($GLOBALS['connection'], $_POST['category_id']);
        $name = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['name']));
        $description = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['description']));
        $status = $_POST['status'];
        
        // Validation
        if (empty($name)) {
            $_SESSION['message'] = "Category name is required.";
            post_redirect("categories.php");
            return;
        }
        
        // Handle image upload (optional)
        $image_update = '';
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['category_image'], 'category_');
            if ($upload_result['success']) {
                $image_update = ", category_image = '" . $upload_result['filename'] . "'";
            } else {
                // Log the error but continue without image update
                $_SESSION['message'] = "Category updated successfully, but image upload failed: " . $upload_result['error'];
            }
        }
        
        $query = "UPDATE categories SET category_name = '$name', category_description = '$description', category_status = '$status' $image_update WHERE category_id = '$id'";
        $result = single_query($query);
        
        if ($result) {
            if (empty($_SESSION['message'])) {
                $_SESSION['message'] = "Category updated successfully!";
            }
        } else {
            $_SESSION['message'] = "Failed to update category. Error: " . mysqli_error($GLOBALS['connection']);
        }
        post_redirect("categories.php");
    }
}

function delete_product()
{
    if (isset($_GET['delete_product'])) {
        $product_id = $_GET['delete_product'];
        $query = "DELETE FROM item WHERE item_id = '$product_id'";
        $result = single_query($query);
        
        if ($result) {
            $_SESSION['message'] = "Product deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete product.";
        }
        
        // Redirect back to the specific category if we came from there
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            post_redirect("categories.php?id=" . $_GET['category']);
        } else {
            post_redirect("categories.php");
        }
    }
}

function get_active_categories()
{
    $query = "SELECT * FROM categories WHERE category_status = 1 ORDER BY category_name";
    $data = query($query);
    return $data;
}

// admin functions (end)
// order functions (start)
function all_orders()
{
    $query = "SELECT * FROM orders";
    $data = query($query);
    return $data;
}
function search_order()
{
    if (isset($_GET['search_order'])) {
        $id = trim($_GET['search_order_id']);
        if (empty($id)) {
            return;
        }
        $query = "SELECT * FROM orders WHERE order_id='$id'";
        $data = query($query);
        if ($data) {
            return $data;
        } else {
            $_SESSION['message'] = "noResultOrder";
            return;
        }
    }
}
function delete_order()
{
    if (isset($_GET['delete'])) {
        $order_id = $_GET['delete'];
        $query = "DELETE FROM orders WHERE order_id ='$order_id'";
        $run = single_query($query);
        get_redirect("orders.php");
    } elseif (isset($_GET['done'])) {
        $order_id = $_GET['done'];
        $query = "UPDATE orders SET order_status = 1 WHERE order_id='$order_id'";
        single_query($query);
        get_redirect("orders.php");
    } elseif (isset($_GET['undo'])) {
        $order_id = $_GET['undo'];
        $query = "UPDATE orders SET order_status = 0 WHERE order_id='$order_id'";
        single_query($query);
        get_redirect("orders.php");
    }
}
function get_orders_by_date($date) {
    $query = "SELECT o.order_id, o.order_quantity, i.item_title as product_name, i.item_price 
              FROM orders o 
              JOIN item i ON o.item_id = i.item_id 
              WHERE DATE(o.order_date) = '$date'";
    return query($query);
}
// order functions (end)

// New centralized image upload handler
function handle_image_upload($file, $prefix = '') {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
    }
    
    // Set target directory - try multiple possible paths
    $possible_dirs = [
        dirname(__DIR__) . "/images/",
        __DIR__ . "/../../images/",
        $_SERVER['DOCUMENT_ROOT'] . "/pharmAid/images/"
    ];
    
    $target_dir = null;
    foreach ($possible_dirs as $dir) {
        if (file_exists($dir) || @mkdir($dir, 0755, true)) {
            $target_dir = $dir;
            break;
        }
    }
    
    // If we couldn't create any directory, try a simpler approach
    if (!$target_dir) {
        $target_dir = dirname(__DIR__) . "/images/";
        
        // Try to create with different permissions
        if (!file_exists($target_dir)) {
            @mkdir($target_dir, 0777, true);
            @chmod($target_dir, 0777);
        }
        
        // Final check
        if (!file_exists($target_dir)) {
            return ['success' => false, 'error' => 'Cannot create upload directory. Please create /images/ folder manually with write permissions.'];
        }
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        @chmod($target_dir, 0777);
        if (!is_writable($target_dir)) {
            return ['success' => false, 'error' => 'Upload directory exists but is not writable. Please set permissions to 755 or 777.'];
        }
    }
    
    // Validate file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.'];
    }
    
    // Check file size (limit to 5MB)
    if ($file['size'] > 5000000) {
        return ['success' => false, 'error' => 'File is too large. Maximum size is 5MB.'];
    }
    
    // Validate that it's actually an image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'error' => 'File is not a valid image.'];
    }
    
    // Generate unique filename
    $filename = $prefix . uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file. Directory: ' . $target_dir];
    }
}

// Debug function to check upload configuration
function check_upload_config() {
    $config = [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'file_uploads' => ini_get('file_uploads') ? 'enabled' : 'disabled',
        'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: 'default',
        'images_dir_exists' => file_exists(dirname(__DIR__) . "/images/") ? 'yes' : 'no',
        'images_dir_writable' => is_writable(dirname(__DIR__) . "/images/") ? 'yes' : 'no',
        'current_dir' => __DIR__,
        'parent_dir' => dirname(__DIR__),
        'document_root' => $_SERVER['DOCUMENT_ROOT']
    ];
    
    return $config;
}

function add_item() {
    if (isset($_POST['add_item'])) {
        $name = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['name']));
        $brand = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['brand']));
        $cat = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['cat']));
        $tags = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['tags']));
        $quantity = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['quantity']));
        $price = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['price']));
        $details = mysqli_real_escape_string($GLOBALS['connection'], trim($_POST['details']));

        // Validation
        if (empty($name) || empty($brand) || empty($cat) || empty($tags) || empty($quantity) || empty($price) || empty($details)) {
            $_SESSION['message'] = "All fields are required.";
            post_redirect("categories.php");
            return;
        }
        
        // Validate numeric fields for negative values
        if (!is_numeric($quantity) || $quantity <= 0) {
            $_SESSION['message'] = "Quantity must be a positive number greater than 0.";
            post_redirect("categories.php");
            return;
        }
        
        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['message'] = "Price must be a positive number greater than 0.";
            post_redirect("categories.php");
            return;
        }
        
        // Ensure quantity is an integer
        if (!is_numeric($quantity) || floor($quantity) != $quantity) {
            $_SESSION['message'] = "Quantity must be a whole number.";
            post_redirect("categories.php");
            return;
        }
        
        // Sanitize numeric values
        $quantity = intval($quantity);
        $price = floatval($price);

        // Check if product name already exists
        $check = check_name($name);
        if ($check == 1) {
            $_SESSION['message'] = "A product with this name already exists.";
            post_redirect("categories.php");
            return;
        }

        // Handle image upload (now optional for testing)
        $image = 'default-product.png'; // Default image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['image'], 'product_');
            if ($upload_result['success']) {
                $image = $upload_result['filename'];
            } else {
                // Continue with default image but show warning
                $_SESSION['message'] = "Product created successfully, but image upload failed: " . $upload_result['error'];
            }
        }

        $query = "INSERT INTO item (item_title, item_brand, category_id, item_details, item_tags, item_image, item_quantity, item_price, item_status) VALUES ('$name', '$brand', '$cat', '$details', '$tags', '$image', '$quantity', '$price', 1)";
        $run = single_query($query);
        if ($run) {
            // Get category name for success message
            $cat_query = "SELECT category_name FROM categories WHERE category_id = '$cat'";
            $cat_data = query($cat_query);
            $category_name = $cat_data ? $cat_data[0]['category_name'] : 'Unknown Category';
            if (empty($_SESSION['message'])) {
                $_SESSION['message'] = "Product added successfully to '$category_name' category!";
            }
        } else {
            $_SESSION['message'] = "Failed to add product. Error: " . mysqli_error($GLOBALS['connection']);
        }
        
        // Redirect back to the specific category if we came from there
        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            get_redirect("categories.php?id=" . $_POST['category_id']);
        } else {
            get_redirect("categories.php");
        }
    } elseif (isset($_POST['cancel'])) {
        // Cancel also redirects back to the specific category if we came from there
        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            get_redirect("categories.php?id=" . $_POST['category_id']);
        } else {
            get_redirect("categories.php");
        }
    }
}
function edit_item($id = null) {
    if (isset($_POST['update'])) {
        global $connection;
        
        // Get ID from POST if not passed as parameter
        if ($id === null && isset($_POST['item_id'])) {
            $id = $_POST['item_id'];
        }
        
        if (empty($id)) {
            $_SESSION['message'] = "Invalid product ID.";
            get_redirect("categories.php");
            return;
        }
        
        $name = mysqli_real_escape_string($connection, trim($_POST['name']));
        $brand = mysqli_real_escape_string($connection, trim($_POST['brand']));
        $cat = mysqli_real_escape_string($connection, trim($_POST['cat']));
        $tags = mysqli_real_escape_string($connection, trim($_POST['tags']));
        $quantity = mysqli_real_escape_string($connection, trim($_POST['quantity']));
        $price = mysqli_real_escape_string($connection, trim($_POST['price']));
        $details = mysqli_real_escape_string($connection, trim($_POST['details']));
        
        if (empty($name) || empty($brand) || empty($cat) || empty($tags) || empty($quantity) || empty($price) || empty($details)) {
            $_SESSION['message'] = "All fields are required.";
            get_redirect("categories.php");
            return;
        }
        
        // Validate numeric fields for negative values
        if (!is_numeric($quantity) || $quantity <= 0) {
            $_SESSION['message'] = "Quantity must be a positive number greater than 0.";
            get_redirect("categories.php");
            return;
        }
        
        if (!is_numeric($price) || $price <= 0) {
            $_SESSION['message'] = "Price must be a positive number greater than 0.";
            get_redirect("categories.php");
            return;
        }
        
        // Ensure quantity is an integer
        if (!is_numeric($quantity) || floor($quantity) != $quantity) {
            $_SESSION['message'] = "Quantity must be a whole number.";
            get_redirect("categories.php");
            return;
        }
        
        // Sanitize numeric values
        $quantity = intval($quantity);
        $price = floatval($price);
        
        // Check if another product with same name exists (excluding current product)
        $check_query = "SELECT item_id FROM item WHERE item_title = '$name' AND item_id != '$id'";
        $existing = query($check_query);
        if (!empty($existing)) {
            $_SESSION['message'] = "A product with this name already exists.";
            get_redirect("categories.php");
            return;
        }
        
        // Handle image upload (optional)
        $image_update = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_result = handle_image_upload($_FILES['image'], 'product_');
            if ($upload_result['success']) {
                $image_update = ", item_image = '" . $upload_result['filename'] . "'";
            } else {
                $_SESSION['message'] = "Product updated successfully, but image upload failed: " . $upload_result['error'];
            }
        }

        $query = "UPDATE item SET item_title='$name', item_brand='$brand', category_id='$cat', item_details='$details', item_tags='$tags', item_quantity='$quantity', item_price='$price' $image_update WHERE item_id='$id'";
        
        $run = single_query($query);
        if ($run) {
            if (empty($_SESSION['message'])) {
                $_SESSION['message'] = "Product updated successfully!";
            }
        } else {
            $_SESSION['message'] = "Failed to update product. Error: " . mysqli_error($connection);
        }
        
        // Redirect back to the specific category if we came from there
        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            get_redirect("categories.php?id=" . $_POST['category_id']);
        } else {
            get_redirect("categories.php");
        }
        
    } elseif (isset($_POST['cancel'])) {
        // Redirect back to the specific category if we came from there
        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            get_redirect("categories.php?id=" . $_POST['category_id']);
        } else {
            get_redirect("categories.php");
        }
    }
}