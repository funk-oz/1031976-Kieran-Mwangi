<?php
session_start();
include "includes/functions.php";
singUp();
?>
<head>
    <meta charset="UTF-8">
    <title>PharmAid</title>
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .panel {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 10px;
        }
        
        .panel-heading {
            background-color: #5bc0de;
            padding: 8px;
            border-bottom: 1px solid #ccc;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -15px -15px 15px -15px;
        }
        
        .panel-title {
            font-weight: bold;
            font-size: 18px;
            color: white;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        .control-label {
            font-weight: bold;
            margin-bottom: 3px;
            display: block;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            height: 35px;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        .btn {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 15px;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .password-info {
            margin: 8px 0;
            font-size: 11px;
            color: #555;
        }
        
        .password-info ul {
            padding-left: 15px;
            margin: 3px 0;
        }
        
        .password-info li {
            margin: 2px 0;
        }
        
        .controls {
            text-align: center;
        }
        
        .signin-link {
            border-top: 1px solid #888;
            padding-top: 12px;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }
        
        .signin-link a {
            text-decoration: none;
            color:#5bc0de;
        }
        
        .signin-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 8px 12px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 13px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Responsive adjustments */
        @media (max-height: 700px) {
            .container {
                max-height: 95vh;
            }
            .form-group {
                margin-bottom: 10px;
            }
            .password-info {
                font-size: 10px;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div id="signupbox">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">Sign Up</div>
            </div>
            <?php message(); ?>
            <div class="panel-body">
                <form id="signupform" method="post" action="signUp.php">
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <div>
                            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="control-label">First Name</label>
                        <div>
                            <input type="text" class="form-control" name="Fname" placeholder="First Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label">Last Name</label>
                        <div>
                            <input type="text" class="form-control" name="Lname" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <div>
                            <input type="text" class="form-control" name="address" placeholder="Address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="control-label">Phone Number</label>
                        <div>
                            <input type="tel" class="form-control" name="phone" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="control-label">Gender</label>
                        <div>
                            <select class="form-control" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <div>
                            <input type="password" class="form-control" name="passwd" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="password-info">
                        <b>Password must contain the following:</b>
                        <ul style="padding-left: 20px; margin-top: 5px;">
                            <li>at least 1 number and 1 letter</li>
                            <li>Must be 8-30 characters</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <div class="controls">
                            <input id="btn-login" class="btn btn-success" type="submit" value="Sign Up" name="singUp" />
                        </div>
                    </div>
                    <div class="signin-link">
                        You already have an account?
                        <a href="login.php">Sign In Here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>