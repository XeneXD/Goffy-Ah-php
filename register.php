<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
            height: 100vh; 
            margin: 0;
            display: flex;
            justify-content: center; 
            align-items: center;
            background-color: #f0f0f0;
            font-family: "Poppins", sans-serif;
            font-weight: 400;
        }        
        .registration-container {
            width: 35vw;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .registration-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
        }
        .action-buttons button {
            padding: 12px 18px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            width: 48%;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .reset-btn {
            background-color: #f44336;
            color: white;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .reset-btn:hover {
            background-color: #e53935;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #0275d8;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h1>Register User</h1>
        <form action="" method="post">
            <div class="input-group">
                <label for="user">Username</label>
                <input type="text" name="user" id="user" required>
            </div>
            <div class="input-group">
                <label for="pass">Password</label>
                <input type="password" name="pass" id="pass" required>
            </div>
            <div class="input-group">
                <label for="verify">Verify Password</label>
                <input type="password" name="verify" id="verify" required>
            </div>
            <div class="action-buttons">
                <button type="submit" name="reg" class="submit-btn">Register</button>
                <button type="reset" class="reset-btn">Clear</button>
            </div>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Sign in here</a>
        </div>
    </div>
</body>
</html>

<?php
    if (isset($_POST['reg'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $verify = $_POST['verify'];

        if ($pass == $verify) {
            echo "Valid Password has been implemented";
        } else {
            echo "Password Not Match";
        }

        try {
            $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Failed: " . $e->getMessage();
        }

        $sql = "INSERT INTO appusers VALUES (null, ?, ?)";
        $insertPreparedStatement = $pdo->prepare($sql);
        $password = password_hash($pass, PASSWORD_DEFAULT);
        $insertPreparedStatement->bindParam(1, $user, PDO::PARAM_STR);
        $insertPreparedStatement->bindParam(2, $password, PDO::PARAM_STR);

        $result = $insertPreparedStatement->execute();
    }
?>