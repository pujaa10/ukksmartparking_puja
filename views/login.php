<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_POST['login'])) {

    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");

    if (!$q) {
        die("Query error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($q);

    if ($data) {

        if (password_verify($pass, $data['password']) || $pass == $data['password']) {

            $_SESSION['login'] = true;
            $_SESSION['username'] = $data['username'];

            // ⬇️ PENTING: arahkan ke controller
            header("Location: ../controllers/ParkirController.php");
            exit;

        } else {
            $error = "❌ Password salah!";
        }

    } else {
        $error = "❌ Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* { 
  margin: 0; 
  padding: 0; 
  box-sizing: border-box; 
  font-family: "Poppins", sans-serif; } 

  body { 
  min-height: 100vh; 
  display: flex; 
  justify-content: center; 
  align-items: center; 
  background: #dfe9ff; } 

  .container-auth { 
  width: 850px; 
  height: 480px; 
  background: white; 
  border-radius: 35px; 
  display: flex; 
  overflow: hidden; 
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12); } 

  .auth-left { 
  width: 50%; 
  padding: 55px 50px; } 
  .auth-left h2 { 
  font-size: 38px; 
  font-weight: 500; 
  margin-bottom: 15px; 
  color: #222; } 

  /* PESAN ERROR */ 
  .error-msg {
  background: #ffe5e5; color: #d60000; 
  padding: 10px; 
  border-radius: 10px; 
  margin-bottom: 15px; 
  font-size: 14px; } 

  .input-box { 
  width: 100%; 
  height: 42px; 
  background: #efefef; 
  border-radius: 25px; 
  display: flex; 
  align-items: center; 
  padding: 0 15px; 
  margin-bottom: 15px; } 

  .input-box i { 
  font-size: 14px; 
  margin-right: 10px; 
  color: #111; } 

  .input-box input { 
  border: none; 
  outline: none; 
  width: 100%; 
  background: transparent; 
  font-size: 13px; 
  color: #444; } 

  button { 
  width: 100%; 
  height: 40px; 
  border: none; 
  border-radius: 25px; 
  background: #3b73ff; 
  color: white; 
  font-size: 13px; 
  cursor: pointer; 
  margin-top: 5px; } 

  button:hover { 
  background: #2458d8; } 
  .auth-right { 
  width: 50%; 
  display: flex; 
  justify-content: center; 
  align-items: center; } 

  .auth-right img { 
  width: 80%; } 

  .logo-title { 
  display: flex; 
  align-items: center; 
  gap: 8px; 
  margin-bottom: 25px; } 

  .logo-title img { 
  width: 32px; 
  height: 32px; 
  object-fit: contain; } 
  
  .logo-title span { 
  font-size: 18px; 
  font-weight: 600; 
  color: #3b73ff; } 
  
  .container-auth { 
  position: relative; } 
  .container-auth::before { 
  content: ""; 
  position: absolute; 
  top: 18px; 
  left: 20px; 
  right: 20px; 
  height: 2px; 
  background: #dbeafe; }
</style>
</head>

<body>

<div class="container-auth">

  <div class="auth-left">

    <div class="logo-title">
        <img src="../logo.png" alt="Logo Parking">
        <span>Parking</span>
    </div>

    <h2>Log in</h2>

    <?php if (isset($error)) { ?>
      <div class="error-msg">
        <?= $error ?>
      </div>
    <?php } ?>

    <form method="POST">

      <div class="input-box">
        <i class="fa fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>

      <div class="input-box">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" name="login">Log in</button>

    </form>
  </div>

  <div class="auth-right">
    <img src="../motor.png" alt="Parking Illustration">
  </div>

</div>

</body>
</html>