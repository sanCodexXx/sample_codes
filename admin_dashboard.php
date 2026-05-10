<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dashboard.php");
    exit;
}

$admin_password = "admin123";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password_check'])) {
    if ($_POST['password'] == $admin_password) {
        $_SESSION['simple_admin'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $login_error = "Wrong password!";
    }
}

if (!isset($_SESSION['simple_admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
        <link rel="shortcut icon" href="assets/logo.png">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui; }
            body {
                background: url('assets/restaurant.jpg') no-repeat center center fixed;
                background-size: cover;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            body::before {
                content: "";
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: inherit;
                filter: blur(3px);
                z-index: -1;
            }
            .login-box {
                background: rgba(116, 91, 78, 0.95);
                backdrop-filter: none;
                padding: 2.5rem;
                border-radius: 1rem;
                border: 1px solid rgba(166,123,91,0.5);
                width: 350px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.6);
                color: #EDE0D4;
                text-align: center;
            }
            .login-box img.form-logo { width: 100px; filter: brightness(0) invert(1); margin-bottom: 1rem; }
            .login-box h2 { color: #F5DEB3; margin-bottom: 1.5rem; font-size: 2rem; }
            input[type="password"] {
                width: 100%;
                padding: 0.85rem;
                margin: 0.5rem 0 1.2rem;
                border: none;
                border-radius: 0.75rem;
                background: rgba(255,251,245,0.9);
                color: #3E2723;
                font-size: 1rem;
            }
            button {
                width: 100%;
                padding: 0.85rem;
                background: #A67B5B;
                color: #fff;
                border: none;
                border-radius: 0.75rem;
                font-weight: bold;
                font-size: 1rem;
                cursor: pointer;
                transition: background 0.3s;
            }
            button:hover { background: #8B5A3C; }
            .error { color: #ff6b6b; margin-bottom: 1rem; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <img src="assets/logo.png" class="form-logo" alt="Logo">
            <h2>Admin Panel</h2>
            <?php if (!empty($login_error)) echo '<p class="error">' . $login_error . '</p>'; ?>
            <form method="post">
                <input type="password" name="password" placeholder="Enter password" required>
                <button type="submit" name="password_check">Sign In</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

include("db.php");
$message = "";

$islandRegions = [
    'Luzon' => ['Region1 (Ilocos)', 'Region2 (Cagayan Valley)', 'Region3 (Central Luzon)', 'Region4-A (CALABARZON)', 'Region4-B (MIMAROPA)', 'Region5 (Bicol)', 'CAR'],
    'Visayas' => ['Region6 (Western Visayas)', 'Region7 (Central Visayas)', 'Region8 (Eastern Visayas)'],
    'Mindanao' => ['Region9 (Zamboanga Peninsula)', 'Region10 (Northern Mindanao)', 'Region11 (Davao)', 'Region12 (SOCCSKSARGEN)', 'Region13 (Caraga)', 'BARMM']
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_food'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $island = trim($_POST['island']);
    $region_code = trim($_POST['region_code']);
    $region = $island . " - " . $region_code;
    $image_path = "";

    if ($_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace('/[^a-z0-9]/i', '_', $name) . "." . $ext;
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        } else {
            $message = "Image upload failed.";
        }
    }

    if ($name && $island && $region_code) {
        $stmt = $conn->prepare("INSERT INTO menu (name, island, description, price, region, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdss", $name, $island, $desc, $price, $region, $image_path);
        if ($stmt->execute()) {
            $message = "Dish added successfully!";
        } else {
            $message = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Name, island and region are required.";
    }
}

if (isset($_GET['delete_dish'])) {
    $id = intval($_GET['delete_dish']);
    $conn->query("DELETE FROM menu WHERE id = $id");
    $message = "Dish deleted.";
    header("Location: admin_dashboard.php?tab=dishes");
    exit;
}

$edit_dish = null;
$edit_region_code = '';
if (isset($_GET['edit_dish'])) {
    $edit_id = intval($_GET['edit_dish']);
    $edit_result = $conn->query("SELECT * FROM menu WHERE id = $edit_id");
    if ($edit_result && $edit_result->num_rows == 1) {
        $edit_dish = $edit_result->fetch_assoc();
        $dashPos = strpos($edit_dish['region'], ' - ');
        $edit_region_code = ($dashPos !== false) ? substr($edit_dish['region'], $dashPos + 3) : $edit_dish['region'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_food'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $island = trim($_POST['island']);
    $region_code = trim($_POST['region_code']);
    $region = $island . " - " . $region_code;
    $image_path = $_POST['existing_image'] ?? '';

    if ($_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace('/[^a-z0-9]/i', '_', $name) . "." . $ext;
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("UPDATE menu SET name=?, island=?, description=?, price=?, region=?, image_path=? WHERE id=?");
    $stmt->bind_param("sssdssi", $name, $island, $desc, $price, $region, $image_path, $id);
    if ($stmt->execute()) {
        $message = "Menu updated!";
        unset($edit_dish);
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id = $uid");
    $message = "User deleted.";
    header("Location: admin_dashboard.php?tab=users");
    exit;
}

$dishes = $conn->query("SELECT * FROM menu ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$users  = $conn->query("SELECT id, username, email FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

$tab = $_GET['tab'] ?? 'dishes';

function active($tabname, $current) {
    return $tabname === $current ? 'class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Authentic Filipino Cuisine</title>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="assets/logo.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui; }
        body {
            background: url('assets/restaurant.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: inherit;
            filter: blur(6px);
            z-index: -1;
        }

        /* ---------- SIDEBAR – solid ---------- */
        .sidebar {
            width: 260px;
            background: rgba(80, 50, 30, 0.95);
            border-right: 1px solid rgba(255,255,255,0.2);
            color: #EDE0D4;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
            z-index: 100;
        }
        .sidebar.hidden { transform: translateX(-100%); }
        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .sidebar-header img.logo-sidebar { height: 2.8rem; filter: brightness(0) invert(1); }
        .sidebar-header h2 { color: #F5DEB3; font-size: 1.4rem; font-weight: 700; }
        .sidebar-nav { flex: 1; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.9rem 1.5rem;
            color: #F8EED7;
            text-decoration: none;
            transition: 0.2s;
            font-size: 1rem;
        }
        .sidebar-nav a i { font-size: 1.4rem; width: 1.8rem; }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(205,133,63,0.85);
            color: #2E1A0F;
        }

        /* ---------- MAIN CONTENT ---------- */
        .main-content {
            margin-left: 260px;
            padding: 2.5rem;
            min-height: 100vh;
            transition: margin-left 0.3s;
        }
        .main-content.expanded { margin-left: 0; }

        .card,
        table {
            background: rgba(99, 78, 63, 0.55);
            backdrop-filter: blur(0px);
            -webkit-backdrop-filter: blur(0px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 1.2rem;
            color: #F2E1C0;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        }
        .card {
            padding: 2rem;
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
            overflow: hidden;
        }
        h1 { color: #d7874a; margin-bottom: 1.5rem; font-size: 2rem; font-weight: 700; }

        th, td {
            padding: 1rem 1.2rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        th {
            background: rgba(80, 45, 25, 0.8);
            color: #F5DEB3;
            font-weight: 600;
            font-size: 0.95rem;
        }
        td {
            color: #D2B48C;
        }
        tbody tr:hover {
            background: rgba(255,255,255,0.1);
        }

        .btn {
            background: #A67B5B;
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            cursor: pointer;
            text-decoration: none;
            margin: 0.2rem;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            transition: 0.3s;
        }
        .btn:hover { background: #8B5A3C; transform: translateY(-1px); }
        .btn.delete { background: #c96f6f; }
        .btn.delete:hover { background: #a84c4c; }
        .btn.edit { background: #6b8c42; }
        .btn.edit:hover { background: #52702e; }

        /* ---------- FORM ELEMENTS ---------- */
        .form-group { margin-bottom: 1.4rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.4rem; color: #EDE0D4; }
        input, select, textarea {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 0.6rem;
            background: rgba(255,251,245,0.9);
            color: #3E2723;
        }
        .message {
            background: rgba(107,142,35,0.8);
            color: #fff;
            padding: 0.9rem 1.2rem;
            border-radius: 0.8rem;
            margin-bottom: 1.5rem;
        }
        img.thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .half-width { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar:not(.hidden) { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .half-width { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="assets/logo.png" alt="Logo" class="logo-sidebar">
        <h2>Admin Panel</h2>
    </div>
    <div class="sidebar-nav">
        <a href="?tab=add_dish" <?= active('add_dish', $tab) ?>><i class='bx bx-plus-circle'></i> Add Menu</a>
        <a href="?tab=dishes" <?= active('dishes', $tab) ?>><i class='bx bx-food-menu'></i> View Menu</a>
        <a href="?tab=users" <?= active('users', $tab) ?>><i class='bx bx-user'></i> View Users</a>
        <a href="?logout=1"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</aside>

<div class="main-content" id="mainContent">
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($tab === 'dishes'): ?>
        <h1>Product Inventory</h1>
        <table>
            <thead>
                <tr><th>ID</th><th>Image</th><th>Name</th><th>Island</th><th>Region</th><th>Price</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($dishes as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td>
                        <?php if ($d['image_path'] && file_exists($d['image_path'])): ?>
                            <img src="<?= htmlspecialchars($d['image_path']) ?>" class="thumb" onerror="this.style.display='none'">
                        <?php else: ?><span>—</span><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($d['name']) ?></td>
                    <td><?= htmlspecialchars($d['island']) ?></td>
                    <td><?= htmlspecialchars(str_replace($d['island'] . ' - ', '', $d['region'])) ?></td>
                    <td>₱<?= number_format($d['price'], 2) ?></td>
                    <td>
                        <a href="?tab=edit_dish&edit_dish=<?= $d['id'] ?>" class="btn edit">Update</a>
                        <a href="?delete_dish=<?= $d['id'] ?>" class="btn delete" onclick="return confirm('Delete this dish?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($tab === 'add_dish'): ?>
        <h1>Add New Menu</h1>
        <div class="card">
            <form method="post" enctype="multipart/form-data">
                <div class="half-width">
                    <div class="form-group">
                        <label>Island *</label>
                        <select name="island" id="add-island" required onchange="updateRegionDropdown('add-island', 'add-region')">
                            <option value="">-- Select Island --</option>
                            <?php foreach (array_keys($islandRegions) as $isl): ?>
                                <option value="<?= $isl ?>"><?= $isl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Region *</label>
                        <select name="region_code" id="add-region" required disabled><option value="">-- Island first --</option></select>
                    </div>
                </div>
                <div class="form-group"><label>Food Name *</label><input type="text" name="name" required></div>
                <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" value="0"></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <div class="form-group"><label>Image (JPG, PNG)</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></div>
                <button type="submit" name="add_food" class="btn">Add Menu</button>
            </form>
        </div>

    <?php elseif ($tab === 'edit_dish' && isset($edit_dish)): ?>
        <h1>Edit Dish: <?= htmlspecialchars($edit_dish['name']) ?></h1>
        <div class="card">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $edit_dish['id'] ?>">
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_dish['image_path']) ?>">
                <div class="half-width">
                    <div class="form-group">
                        <label>Island *</label>
                        <select name="island" id="edit-island" required onchange="updateRegionDropdown('edit-island', 'edit-region')">
                            <?php foreach (array_keys($islandRegions) as $isl): ?>
                                <option value="<?= $isl ?>" <?= $edit_dish['island'] == $isl ? 'selected' : '' ?>><?= $isl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Region *</label>
                        <select name="region_code" id="edit-region" required><option value="">-- Choose --</option></select>
                    </div>
                </div>
                <div class="form-group"><label>Food Name *</label><input type="text" name="name" value="<?= htmlspecialchars($edit_dish['name']) ?>" required></div>
                <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" value="<?= $edit_dish['price'] ?>"></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($edit_dish['description']) ?></textarea></div>
                <div class="form-group">
                    <label>Current Image</label>
                    <?php if ($edit_dish['image_path'] && file_exists($edit_dish['image_path'])): ?>
                        <img src="<?= htmlspecialchars($edit_dish['image_path']) ?>" style="width:100px; border-radius:0.5rem;"><br>
                    <?php else: ?><span>No image</span><br><?php endif; ?>
                    <label>Replace Image</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                </div>
                <button type="submit" name="update_food" class="btn edit">Update Menu</button>
                <a href="?tab=dishes" class="btn delete">Cancel</a>
            </form>
        </div>

    <?php elseif ($tab === 'users'): ?>
        <h1>Registered Users</h1>
        <table>
            <thead>
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><a href="?delete_user=<?= $u['id'] ?>" class="btn delete" onclick="return confirm('Delete this user?')">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php header("Location: ?tab=dishes"); exit; ?>
    <?php endif; ?>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('hidden');
        document.getElementById('mainContent').classList.toggle('expanded');
    }

    const islandRegions = <?= json_encode($islandRegions) ?>;
    function updateRegionDropdown(islandSelectId, regionSelectId) {
        const islandSelect = document.getElementById(islandSelectId);
        const regionSelect = document.getElementById(regionSelectId);
        const island = islandSelect.value;
        regionSelect.innerHTML = '<option value="">-- Choose region --</option>';
        if (island && islandRegions[island]) {
            islandRegions[island].forEach(function(reg) {
                const option = document.createElement('option');
                option.value = reg;
                option.textContent = reg;
                regionSelect.appendChild(option);
            });
            regionSelect.disabled = false;
        } else {
            regionSelect.disabled = true;
        }
    }

    window.addEventListener('DOMContentLoaded', function () {
        const editIsland = document.getElementById('edit-island');
        const editRegion = document.getElementById('edit-region');
        if (editIsland && editRegion) {
            updateRegionDropdown('edit-island', 'edit-region');
            const existingRegionCode = "<?= htmlspecialchars($edit_region_code ?? '') ?>";
            if (existingRegionCode) {
                for (let opt of editRegion.options) {
                    if (opt.value === existingRegionCode) { opt.selected = true; break; }
                }
            }
        }
    });
</script>
<?php $conn->close(); ?>
</body>
</html>