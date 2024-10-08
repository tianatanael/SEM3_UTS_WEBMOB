<?php
require 'user_session.php';
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Menggunakan prepared statement untuk mencegah SQL injection
  $stmt = $conn->prepare("SELECT staffid, username, password FROM login WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
      // Bind hasil
      //Menyimpan data dari database ke variabel
      // staffid di variabel $staffid, username di $db_username, password di $db_password
      $stmt->bind_result($staffid, $db_username, $db_password);
      $stmt->fetch();

       // Verify if the username fetched matches the input username
      if ($username == $db_username){
          //Verify the password
          if (password_verify($password, $db_password)){
              $_SESSION['staffid'] = $staffid;
              header("Location: index.php");
              exit(); 
          }else{
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Password salah.'];
          }
      }else{
        $_SESSION['message'] = ['type' => 'error', 'text' => 'username salah.'];
      }
  } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Username tidak ditemukan.'];
  }

  $stmt->close();
}

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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jaya Gudang</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/JAYA_GUDANG/AdminLte/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="/JAYA_GUDANG/AdminLte/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/JAYA_GUDANG/AdminLte/css/adminlte.min.css">

    <!-- Include jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../../index2.html" class="h1"><b>Jaya Gudang</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Log in to start your session</p>

                <form method="post" action="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <!-- Additional space for other controls if needed -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <!-- FORGOT PASSWORD -->
                <!-- <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p> -->

                <!-- REGISTER -->
                <p class="mb-0">
                    <a href="register.php" class="text-center">Register a new member</a>
                </p>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>

    // Show message if it exists in the session
    <?php if ($message): ?>
        Swal.fire({
            title: '<?php echo $message['type'] === 'success' ? 'Success!' : 'Error!'; ?>',
            text: '<?php echo $message['text']; ?>',
            icon: '<?php echo $message['type'] === 'success' ? 'success' : 'error'; ?>'
        });
    <?php endif; ?>  

</script>