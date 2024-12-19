<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

$colleges = [];
$deptfullname = '';
$deptshortname = '';
$deptcollid = '';
$deptid = null;

try {
    $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
    $stmt = $pdo->query("SELECT collid, collfullname FROM colleges");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}

if (isset($_GET['id'])) {
    $deptid = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM departments WHERE deptid = ?");
        $stmt->execute([$deptid]);
        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($department) {
            $deptfullname = $department['deptfullname'];
            $deptshortname = $department['deptshortname'];
            $deptcollid = $department['deptcollid'];
        } else {
            $_SESSION['error'] = "Department not found.";
            header("Location: departments.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . htmlspecialchars($e->getMessage());
        header("Location: departments.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptid = $_POST['deptid'] ?? null;
    $deptfullname = $_POST['deptfullname'];
    $deptshortname = $_POST['deptshortname'];
    $deptcollid = $_POST['deptcollid'];

    try {
        $pdo = new PDO("mysql:host=localhost:3306;dbname=usjr", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($deptid) {
            $stmt = $pdo->prepare("
                UPDATE departments
                SET deptfullname = ?, deptshortname = ?, deptcollid = ?
                WHERE deptid = ?
            ");
            $stmt->execute([$deptfullname, $deptshortname, $deptcollid, $deptid]);
        } else {
            $stmt = $pdo->query("SELECT IFNULL(MAX(deptid), 0) AS max_deptid FROM departments");
            $new_deptid = $stmt->fetch(PDO::FETCH_ASSOC)['max_deptid'] + 1;

            $stmt = $pdo->prepare("
                INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$new_deptid, $deptfullname, $deptshortname, $deptcollid]);
        }

        $_SESSION['success'] = "Department saved successfully!";
        header("Location: departments.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($_GET['id']) ? 'Edit Department' : 'Add New Department' ?></title>
    <style>
        body {
            background-color: #e8f5e9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2e7d32;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button, .cancel-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin: 0 5px;
        }
        button {
            background-color: #4caf50;
        }
        button:hover {
            background-color: #388e3c;
        }
        .cancel-btn {
            background-color: #d32f2f;
        }
        .cancel-btn:hover {
            background-color: #b71c1c;
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
            background-color: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2><?= isset($_GET['id']) ? 'Edit Department' : 'Add New Department' ?></h2>
    <form id="departmentForm" action="department.entry.php<?= isset($_GET['id']) ? '?id=' . $_GET['id'] : '' ?>" method="POST">
        <div class="form-group">
            <label for="deptid">Department ID</label>
            <input type="number" id="deptid" name="deptid" value="<?= htmlspecialchars($deptid ?? '') ?>" placeholder="Enter department ID" required>
        </div>
        <div class="form-group">
            <label for="deptfullname">Department Full Name</label>
            <input type="text" id="deptfullname" name="deptfullname" value="<?= htmlspecialchars($deptfullname ?? '') ?>" placeholder="Enter department full name" required>
        </div>
        <div class="form-group">
            <label for="deptshortname">Department Short Name</label>
            <input type="text" id="deptshortname" name="deptshortname" value="<?= htmlspecialchars($deptshortname ?? '') ?>" placeholder="Enter department short name" required>
        </div>
        <div class="form-group">
            <label for="deptcollid">College</label>
            <select id="deptcollid" name="deptcollid" required>
                <option value="" disabled <?= empty($deptcollid) ? 'selected' : '' ?>>Select college</option>
                <?php foreach ($colleges as $college): ?>
                    <option value="<?= $college['collid'] ?>" <?= $college['collid'] == $deptcollid ? 'selected' : '' ?>><?= htmlspecialchars($college['collfullname'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="button-group">
            <button type="submit"><?= isset($_GET['id']) ? 'Update Department' : 'Add Department' ?></button>
            <a href="departments.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="overlay" id="errorPopup">
        <div class="popup">
            <h3><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></h3>
            <button class="confirm-btn" onclick="closePopup()">OK</button>
        </div>
    </div>
    <script>
        document.getElementById('errorPopup').style.display = 'flex';
        function closePopup() {
            document.getElementById('errorPopup').style.display = 'none';
        }
    </script>
<?php endif; ?>

<script src="axios.min.js"></script>
<script>
    document.getElementById('departmentForm').addEventListener('submit', async function(event) {
        event.preventDefault(); 

        const formData = new FormData(this);

        try {
            const response = await axios.post('save.departments.php', formData);
            if (response.data.success) {
                window.location.href = 'departments.php';
            } else {
                document.getElementById('errorPopup').style.display = 'flex';
                document.querySelector('#errorPopup h3').textContent = response.data.error || 'Unknown error occurred.';
            }
        } catch (error) {
            document.getElementById('errorPopup').style.display = 'flex';
            document.querySelector('#errorPopup h3').textContent = 'An error occurred while saving. Please try again.';
            console.error(error);
        }
    });

    function closePopup() {
        document.getElementById('errorPopup').style.display = 'none';
    }
</script>
</body>
</html>
