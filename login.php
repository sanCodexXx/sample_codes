<?php
session_start();
include("db.php");

$error_message = "";
$toastBgColor = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid email or password.";
            $toastBgColor = "#dc3545";
        }
    } else {
        $error_message = "No account found with that email.";
        $toastBgColor = "#dc3545";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Authentic Filipino Cuisine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="assets/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui; }

        /* Full-page restaurant background with blur overlay */
        body {
            background: url('assets/restaurant.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
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

        /* Centered glass card */
        .signup-card {
            background: rgba(210, 206, 199, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(166, 123, 91, 0.5);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            width: 420px;
            max-width: 90%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            color: #EDE0D4;
            text-align: center;
        }

        .form-logo {
            width: 100px;
            height: auto;
            filter: brightness(0) invert(1);
            margin-bottom: 1.5rem;
        }

        .form-heading {
            font-size: 1.8rem;
            font-weight: 700;
            color: #F5DEB3;
            margin-bottom: 1.5rem;
        }

        .input-label {
            display: block;
            text-align: left;
            color: #EDE0D4;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .input-label i {
            margin-right: 0.5rem;
            color: #F5DEB3;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 251, 245, 0.9);
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            color: #3E2723;
            margin-bottom: 1rem;
        }
        .form-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(166, 123, 91, 0.5);
        }

        .submit-button {
            width: 100%;
            padding: 0.8rem;
            background: #A67B5B;
            color: #FFF;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 0.5rem;
        }
        .submit-button:hover {
            background: #8B5A3C;
        }

        .login-prompt {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: #D4C4B7;
        }
        .login-link {
            color: #F5DEB3;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link:hover {
            text-decoration: underline;
            color: #CD853F;
        }

        .toast-message {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1050;
            border-radius: 0.5rem;
            min-width: 280px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php if ($error_message): ?>
        <div class="toast align-items-center text-white border-0 toast-message" 
             role="alert" aria-live="assertive" aria-atomic="true"
             style="background-color: <?php echo $toastBgColor; ?>;">
            <div class="d-flex">
                <div class="toast-body"><?php echo $error_message; ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" class="signup-card">
        <img src="assets/logo.png" class="form-logo" alt="Logo">
        <h5 class="form-heading">Login</h5>

        <label class="input-label"><i class="fa fa-envelope"></i> Email</label>
        <input type="email" name="email" class="form-input" required>

        <label class="input-label"><i class="fa fa-lock"></i> Password</label>
        <input type="password" name="password" class="form-input" required>

        <button type="submit" class="submit-button">Login</button>

        <div class="login-prompt">
            Don't have an account? <a href="./register.php" class="login-link">Sign up</a>
        </div>
    </form>

    <script>
        let toastElList = [].slice.call(document.querySelectorAll('.toast'));
        let toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 3000 });
        });
        toastList.forEach(toast => toast.show());
    </script>
</body>
</html>