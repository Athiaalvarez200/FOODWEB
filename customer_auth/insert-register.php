<?php
session_start();
include('../config.php');

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmpassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $signin_provider = "email";

    if (!preg_match("/^[A-Z a-z]{2,30}$/", $name)) {
        $_SESSION["invalid_name"] = "Name should contain alphabet only";
        header("Location:./register.php");
        exit();
    }

    if (!preg_match("/^[0-9A-Za-z_-]{2,30}$/", $username)) {
        $_SESSION["invalid_username"] = "Username shouldn't contain special characters";
        header("Location:./register.php");
        exit();
    }

    if (!preg_match("/^[a-zA-Z0-9._]+@[a-zA-Z0-9.]+\.[a-zA-Z]{2,}$/", $email)) {
        $_SESSION["invalid_email"] = "Invalid email";
        header("Location:./register.php");
        exit();
    }

    if (!preg_match("/^[0-9A-Z a-z,!@#$%^&*()_+]{8,50}$/", $password)) {
        $_SESSION["invlaid_password"] = "Password should contain minimum 8 characters";
        header("Location:./register.php");
        exit();
    }

    if ($password != $confirmpassword) {
        $_SESSION["password_not_match"] = "Password doesn't match";
        header("Location:./register.php");
        exit();
    }

    $sql_username = "SELECT username FROM customer WHERE username='$username'";
    $res_username = mysqli_query($conn, $sql_username) or die("Error");

    $sql_email = "SELECT email FROM customer WHERE email='$email'";
    $res_email = mysqli_query($conn, $sql_email) or die("Error");

    if (mysqli_num_rows($res_username) > 0) {
        $_SESSION["username_already_exit"] = "Username already exists";
        header("Location:./register.php");
        exit();
    }

    if (mysqli_num_rows($res_email) > 0) {
        $_SESSION["email_already_exit"] = "Email already exists";
        header("Location:./register.php");
        exit();
    }

    $code = 0;
    $count = 0;
    $password = md5($password);
    $status = "verified";

    $sql = "INSERT INTO customer VALUES 
    (default, '$name', '$username', '$email', '$password', '$signin_provider', NOW(), '$status', 1, $code, $count)";

    $res = mysqli_query($conn, $sql) or die("Error");

    if ($res) {
        $_SESSION['success'] = true;
        $_SESSION['username'] = $username;

        header("Location:../index.php");
        exit();
    }
} else {
    header("Location:./register.php");
    exit();
}
?>