<?php
require 'user_session.php';
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $code = $_POST['code'];
    $password = $_POST['password'];
    $confirm_pwd = $_POST['confirm_password'];

    // Validate password
    if ($password !== $confirm_pwd) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Passwords do not match!'];
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Expected code value
    $expected_code = '1009';

    // Check if the code is correct
    if ($code !== $expected_code) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Wrong Code'];
        header("Location: register.php");
        exit();
    }

    // Check if the email or username already exists
    $stmt = $conn->prepare("SELECT staffid FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email or username already exists
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Username already exists.'];
        $stmt->close();
        header("Location: register.php");
        exit();
    } else {
        // Proceed with insertion
        $stmt->close();

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO login (username, nama, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $nama, $hashed_password);

        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;

            // Set a success message
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Your registration was successful! Your User ID is: ' . urlencode($last_id)];// or just "Registration successful!" if you don't want to show ID
            header('Location: register.php');
        } else {
            $_SESSION['message'] = 'Registration failed: ' . $stmt->error;
        }

        $stmt->close();
    }

    // Redirect back to register.php to show the message
    header("Location: register.php");
    exit();
}

$conn->close();

// Simpan pesan ke variabel dan hapus dari session
$message = null;
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Bootstrap 5 source -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <link rel="stylesheet" href="/LAT_HRD/CSS/register_1.css">
    <link rel="stylesheet" href="/LAT_HRD/CSS/register_2.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</head>
<body>

    <div class="container">

        <div class="card card-info">
            <div class="card-header text-center">
              <h3 class="card-title"><strong>Register</strong></h3>
            </div>
            <!-- /.card-header -->

            

            <!-- form start -->
            <form class="form-horizontal" method="POST" action="register.php">
              <div class="card-body">


            
            <div class="form-group row">
                <div class="input-group">
                    <span class="icon"><h5><i class="fas fa-user"></i></h5></span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="input-group">
                    <span class="icon"><h5><i class="fas fa-id-badge"></i></h5></span>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="input-group">
                    <span class="icon"><h5><i class="fas fa-lock"></i></h5></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" minlength="8" required>
                    <span class="password-toggle-icon"><i class="fas fa-eye" id="togglePassword"></i></span>
                </div>
            </div>

            <div class="form-group row">
                <div class="input-group">
                    <span class="icon"><h5><i class="fas fa-lock"></i></h5></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    <span class="password-toggle-icon"><i class="fas fa-eye" id="toggleConfirmPassword"></i></span>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="input-group">
                    <span class="icon"><h5><i class='fas fa-key'></i></h5></span>
                    <input type="text" class="form-control" id="code" placeholder="Code" name="code" required>
                  </div>
                </div>
    
              </div>
              <!-- /.card-body -->
              
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-info register-btn" style="margin-top: 0px;">
                        Register
                    </button>

                    <div class="mt-2">
                        <a href="login.php" class="btn btn-link" >
                            Login
                        </a>
                    </div>
                </div>
                <!-- /.card-footer -->
            </form>
          </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="path/to/bootstrap.bundle.min.js"></script>

</body>

<script>

    // Show message if it exists in the session
    <?php if ($message): ?>
        Swal.fire({
            title: '<?php echo $message['type'] === 'success' ? 'Success!' : 'Error!'; ?>',
            text: '<?php echo $message['text']; ?>',
            icon: '<?php echo $message['type'] === 'success' ? 'success' : 'error'; ?>'
        });
    <?php endif; ?>     

  document.addEventListener("DOMContentLoaded", function() {
            const passwordField = document.getElementById("password");
            const confirmPasswordField = document.getElementById("confirm_password");
            const togglePassword = document.getElementById("togglePassword");
            const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

            togglePassword.addEventListener("click", function () {
              if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.classList.remove("fa-eye");
                togglePassword.classList.add("fa-eye-slash");
              } else {
                passwordField.type = "password";
                togglePassword.classList.remove("fa-eye-slash");
                togglePassword.classList.add("fa-eye");
              }
            });

            toggleConfirmPassword.addEventListener("click", function () {
              if (confirmPasswordField.type === "password") {
                confirmPasswordField.type = "text";
                toggleConfirmPassword.classList.remove("fa-eye");
                toggleConfirmPassword.classList.add("fa-eye-slash");
              } else {
                confirmPasswordField.type = "password";
                toggleConfirmPassword.classList.remove("fa-eye-slash");
                toggleConfirmPassword.classList.add("fa-eye");
              }
            });
          });
</script>

</html>