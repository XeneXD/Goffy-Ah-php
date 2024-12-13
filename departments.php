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
    <title>Departments</title>
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
            background-color: #ffffff;
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

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .popup {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .popup h3 {
            margin: 0 0 10px;
        }

        .popup button {
            margin: 10px 5px 0;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup .confirm-btn {
            background-color: #d9534f;
            color: white;
        }

        .popup .cancel-btn {
            background-color: #ccc;
            color: black;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <div>
            <h1 class="header-title">Departments</h1>
            <p class="user-info">Logged in as: <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></p>
        </div>
        <form action="logout.php" method="POST" class="logout-form">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <!-- Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="action-bar">
        <form action="department.entry.php" method="POST">
            <button type="submit" class="add-btn">Add New Department</button>
        </form>
    </div>

    <h2>Department List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "SELECT collid, collfullname FROM colleges";
                $stmt = $pdo->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['collid']}</td>
                        <td>{$row['collfullname']}</td>
                        <td>
                            <button class='edit-btn' onclick='editDepartment({$row['collid']})'>Edit</button>
                            <button class='delete-btn' onclick='deleteDepartment({$row['collid']})'>Delete</button>
                        </td>
                    </tr>";
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                echo "<tr><td colspan='3'>An error occurred while retrieving department data.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="axios.min.js"></script>
<script>
    function showPopupMessage(message, type) {
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
        overlay.innerHTML = `
            <div class="popup">
                <h3>${message}</h3>
                <button class="confirm-btn">OK</button>
            </div>
        `;
        document.body.appendChild(overlay);
        overlay.style.display = 'flex';

        overlay.querySelector('.confirm-btn').addEventListener('click', () => {
            document.body.removeChild(overlay);
            if (type === 'success') {
                location.reload();
            }
        });
    }

    function editDepartment(id) {
        window.location.href = `department.entry.php?id=${id}`;
    }

    function deleteDepartment(id) {
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
        overlay.innerHTML = `
            <div class="popup">
                <h3>Are you sure you want to delete this department?</h3>
                <button class="confirm-btn">Yes</button>
                <button class="cancel-btn">No</button>
            </div>
        `;
        document.body.appendChild(overlay);
        overlay.style.display = 'flex';

        overlay.querySelector('.confirm-btn').addEventListener('click', () => {
            axios.post('deletedepartment.php', { id: id })
                .then(response => {
                    if (response.data.success) {
                        showPopupMessage('Department deleted successfully', 'success');
                    } else {
                        showPopupMessage('Failed to delete department: ' + response.data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('There was an error!', error);
                    showPopupMessage('An error occurred while deleting the department', 'error');
                });
        });

        overlay.querySelector('.cancel-btn').addEventListener('click', () => {
            document.body.removeChild(overlay);
        });
    }
</script>
</body>
</html>