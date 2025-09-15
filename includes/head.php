<?php
session_start();
require_once __DIR__ . '/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YassirPharm</title>
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link rel="stylesheet" type="text/css" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: static;
            background-color: #f8fafc;
        }
        
        .navbar {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .navbar-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            height: 70px;
        }
        
        .logo {
            color: white;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        .logo::before {
            content: "💊";
            font-size: 24px;
        }
        
        .nav-center {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex: 1;
            justify-content: center;
        }
        
        .nav-links {
            display: flex;
            list-style-type: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-links li {
            position: relative;
        }
        
        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .cart-link {
            position: relative;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #059669;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .cart-badge.empty {
            display: none;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateY(-1px);
        }
        
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .dropdown {
            position: relative;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 220px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 8px;
            animation: dropdownFadeIn 0.3s ease;
        }
        
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-content a {
            color: #374151;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dropdown-content a:last-child {
            border-bottom: none;
        }
        
        .dropdown-content a:hover {
            background: rgba(5, 150, 105, 0.08);
            color:rgb(32, 168, 125);
            transform: translateX(4px);
        }
        
        .show {
            display: block;
        }
        
        .search-container {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 25px;
            padding: 8px 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            min-width: 280px;
        }
        
        .search-container:focus-within {
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }
        
        .search-container input[type="text"] {
            background: transparent;
            border: none;
            outline: none;
            color: white;
            font-size: 14px;
            font-weight: 400;
            flex: 1;
            padding: 6px 12px;
        }
        
        .search-container input[type="text"]::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-container button {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            padding: 6px;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-container button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: scale(1.1);
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .profile-icon {
            color: white;
            font-size: 18px;
        }
        
        .profile-text {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }
        
        .profile-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 250px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 8px;
            animation: dropdownFadeIn 0.3s ease;
        }
        
        .profile-dropdown.show {
            display: block;
        }
        
        .profile-header {
            padding: 20px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            text-align: center;
        }
        
        .profile-header .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            margin: 0 auto 10px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .profile-header .user-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .profile-header .user-email {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-menu {
            padding: 8px 0;
        }
        
        .profile-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .profile-menu a:last-child {
            border-bottom: none;
        }
        
        .profile-menu a:hover {
            background: rgba(5, 150, 105, 0.08);
            color: #059669;
        }
        
        .profile-menu a.logout-link {
            color: #ef4444;
        }
        
        .profile-menu a.logout-link:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .login-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            padding: 8px 20px !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease !important;
        }
        
        .login-btn:hover {
            background: white !important;
            color: #059669 !important;
            transform: translateY(-1px) !important;
        }
        
        .mobile-menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        @media screen and (max-width: 1024px) {
            .navbar-container {
                padding: 0 1.5rem;
            }
            
            .search-container {
                min-width: 220px;
            }
            
            .profile-text {
                display: none;
            }
        }
        
        @media screen and (max-width: 768px) {
            .navbar-container {
                padding: 0 1rem;
                height: 60px;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .nav-center {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .nav-center.show {
                display: flex;
            }
            
            .nav-links {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }
            
            .nav-links li {
                width: 100%;
            }
            
            .nav-links a {
                width: 100%;
                justify-content: center;
                padding: 1rem;
            }
            
            .search-container {
                width: 100%;
                margin-top: 1rem;
                min-width: auto;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .user-section {
                gap: 0.5rem;
            }
            
            .dropdown-content {
                position: static;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                margin: 0.5rem 0;
                border-radius: 8px;
            }
            
            .dropdown-content a {
                color: rgba(255, 255, 255, 0.9);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .dropdown-content a:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                transform: none;
            }
            
            .profile-dropdown {
                right: -1rem;
                left: -1rem;
                margin-top: 0.5rem;
            }
        }
        
        @media screen and (max-width: 480px) {
            .navbar-container {
                padding: 0 0.75rem;
            }
            
            .logo {
                font-size: 20px;
            }
        }
        /* Toast-style alerts */
        .toast-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            min-width: 280px;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 10px;
            opacity: 0.98;
            backdrop-filter: blur(6px);
        }
        .toast-message + .toast-message { /* stack multiple toasts */
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">YassirPharm</div>
            
            <div class="nav-center" id="navCenter">
                <ul class="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="cart-link">
                        <a href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $cart_count = get_cart_count();
                                if ($cart_count > 0) {
                                    echo '<span class="cart-badge">' . $cart_count . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><i class="fas fa-th-large"></i> Categories <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-content" id="myDropdown">
                            <?php
                            $nav_categories = get_active_categories();
                            if (!empty($nav_categories)) {
                                foreach ($nav_categories as $nav_category) {
                                    echo '<a href="search.php?cat=' . urlencode($nav_category['category_name']) . '"><i class="fas fa-pills"></i> ' . htmlspecialchars($nav_category['category_name']) . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </li>
                </ul>
                
                <form class="search-container" action="search.php" method="GET">
                    <input type="text" name="search_text" placeholder="Search medicines, devices...">
                    <button type="submit" name="search" value="go">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <div class="user-section">
                <?php
                if (!isset($_SESSION['user_id'])) {
                    echo "<a class='nav-link login-btn' href='login.php'><i class='fas fa-sign-in-alt'></i> Log in</a>";
                } else {
                    $check_user_id = check_user($_SESSION['user_id']);
                    if ($check_user_id == 1) {
                        // Get user's data for profile
                        $user_data = get_user_data($_SESSION['user_id']);
                        $first_name = isset($user_data['user_fname']) ? $user_data['user_fname'] : 'User';
                        $last_name = isset($user_data['user_Lname']) ? $user_data['user_Lname'] : '';
                        $email = isset($user_data['email']) ? $user_data['email'] : '';
                        $first_letter = strtoupper(substr($first_name, 0, 1));
                        
                        echo '<div class="user-profile" id="userProfile">
                                <i class="fas fa-user profile-icon"></i>
                                <span class="profile-text">Profile</span>
                                <i class="fas fa-chevron-down" style="color: rgba(255, 255, 255, 0.7); font-size: 12px;"></i>
                                
                                <div class="profile-dropdown" id="profileDropdown">
                                    <div class="profile-header">
                                        <div class="user-avatar">' . $first_letter . '</div>
                                        <div class="user-name">' . htmlspecialchars($first_name . ' ' . $last_name) . '</div>
                                        <div class="user-email">' . htmlspecialchars($email) . '</div>
                                    </div>
                                    <div class="profile-menu">
                                        <a href="cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
                                        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                    </div>
                                </div>
                              </div>';
                    } else {
                        post_redirect("logout.php");
                    }
                }
                ?>
                
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <script>
        // Dropdown functionality
        var dropdownBtn = document.querySelector('.dropbtn');
        var dropdownContent = document.getElementById('myDropdown');

        if (dropdownBtn && dropdownContent) {
            dropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownContent.classList.toggle('show');
            });

            window.addEventListener('click', function(e) {
                if (!e.target.matches('.dropbtn') && !e.target.closest('.dropdown')) {
                    if (dropdownContent.classList.contains('show')) {
                        dropdownContent.classList.remove('show');
                    }
                }
            });
        }
        
        // Profile dropdown functionality
        var userProfile = document.getElementById('userProfile');
        var profileDropdown = document.getElementById('profileDropdown');

        if (userProfile && profileDropdown) {
            userProfile.addEventListener('click', function(e) {
                // Only prevent default if clicking on the profile toggle itself, not dropdown content
                if (e.target.closest('.profile-dropdown')) {
                    return; // Allow normal link behavior inside dropdown
                }
                
                e.preventDefault();
                profileDropdown.classList.toggle('show');
                
                // Close categories dropdown if open
                if (dropdownContent && dropdownContent.classList.contains('show')) {
                    dropdownContent.classList.remove('show');
                }
            });

            // Close profile dropdown when clicking outside
            window.addEventListener('click', function(e) {
                if (!e.target.closest('.user-profile')) {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                }
            });
            
            // Allow dropdown links to work normally
            var dropdownLinks = profileDropdown.querySelectorAll('a');
            dropdownLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent dropdown from closing immediately
                    // Allow normal navigation
                });
            });
        }
        
        // Mobile menu functionality
        var mobileMenuToggle = document.getElementById('mobileMenuToggle');
        var navCenter = document.getElementById('navCenter');
        var menuIcon = mobileMenuToggle.querySelector('i');

        if (mobileMenuToggle && navCenter) {
            mobileMenuToggle.addEventListener('click', function() {
                navCenter.classList.toggle('show');
                
                // Toggle hamburger/close icon
                if (navCenter.classList.contains('show')) {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-times');
                } else {
                    menuIcon.classList.remove('fa-times');
                    menuIcon.classList.add('fa-bars');
                }
            });
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (navCenter && navCenter.classList.contains('show')) {
                if (!e.target.closest('.navbar')) {
                    navCenter.classList.remove('show');
                    if (menuIcon) {
                        menuIcon.classList.remove('fa-times');
                        menuIcon.classList.add('fa-bars');
                    }
                }
            }
        });
        
        // Add active class to current page
        document.addEventListener('DOMContentLoaded', function() {
            var currentPage = window.location.pathname.split('/').pop();
            var navLinks = document.querySelectorAll('.nav-links a');
            
            navLinks.forEach(function(link) {
                var linkPage = link.getAttribute('href');
                if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.php')) {
                    link.classList.add('active');
                }
            });
        });
        
        // Navbar scroll effect
        var navbar = document.querySelector('.navbar');
        var lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                navbar.style.background = 'linear-gradient(135deg, rgba(5, 150, 105, 0.95) 0%, rgba(4, 120, 87, 0.95) 100%)';
                navbar.style.backdropFilter = 'blur(15px)';
            } else {
                navbar.style.background = 'linear-gradient(135deg, #059669 0%, #047857 100%)';
                navbar.style.backdropFilter = 'blur(10px)';
            }
            
            lastScrollTop = scrollTop;
        });

        // Auto-dismiss alerts as toasts
        document.addEventListener('DOMContentLoaded', function() {
            var alerts = document.querySelectorAll('.alert, .stock-alert');
            if (alerts && alerts.length) {
                alerts.forEach(function(alert, idx) {
                    // Convert to toast style without changing original classes
                    alert.classList.add('toast-message');
                    // Stagger stacking
                    alert.style.top = (20 + (idx * 68)) + 'px';
                    // Auto-hide after 3.5s
                    setTimeout(function() {
                        alert.style.transition = 'opacity 300ms ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 320);
                    }, 3500);
                });
            }
        });
    </script>
</body>
</html>