<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #ffffff ;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .header-title {
            font-size: 24px;
            color: #333;
        }

        .user-info {
            font-size: 14px;
            color: #555;
        }

        .logout-form {
            margin: 0;
        }

        .logout-btn {
            background-color: #d9534f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #c9302c;
        }

        .action-bar {
            margin: 20px 0;
            text-align: right;
        }

        .add-btn {
            background-color: #0275d8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-btn:hover {
            background-color: #025aa5;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #00FF00;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #e6f7ff;
        }

        .edit-btn {
            background-color: #5cb85c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .edit-btn:hover {
            background-color: #4cae4c;
        }

        .delete-btn {
            background-color: #d9534f;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <div>
                <h1 class="header-title">Dashboard</h1>
                <p class="user-info">Logged in as: <?php echo htmlspecialchars($_SESSION['name']); ?></p>
            </div>
            <form action="logout.php" method="POST" class="logout-form">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </header>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="action-bar">
            <form action="student.entry.php" method="POST">
                <button type="submit" class="add-btn">Add New Student</button>
            </form>
        </div>

        <h2>Student Entries</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Middle Name</th>
                    <th>College</th>
                    <th>Program</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $stmt = $pdo->query("
                        SELECT s.studid, s.studfirstname, s.studlastname, s.studmidname, c.collfullname, p.progfullname, s.studyear
                        FROM students s
                        JOIN colleges c ON s.studcollid = c.collid
                        JOIN programs p ON s.studprogid = p.progid
                    ");

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                            <td>{$row['studid']}</td>
                            <td>{$row['studfirstname']}</td>
                            <td>{$row['studlastname']}</td>
                            <td>{$row['studmidname']}</td>
                            <td>{$row['collfullname']}</td>
                            <td>{$row['progfullname']}</td>
                            <td>{$row['studyear']}</td>
                            <td>
                                <a href='editstudent.php?id={$row['studid']}' class='edit-btn'>Edit</a>
                                <form action='deletestudent.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='{$row['studid']}'>
                                    <button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } catch (PDOException $e) {
                    error_log("Database error: " . $e->getMessage());
                    echo "<tr><td colspan='8'>An error occurred while retrieving student data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
