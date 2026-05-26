<?php
session_start();
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($name) || empty($email)) {
        $_SESSION['invalid'] = "Please enter your Gmail name and email.";
        header("Location: google_auth.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['invalid'] = "Please enter a valid email address.";
        header("Location: google_auth.php");
        exit;
    }

    $sql_email = "SELECT active, id FROM customer WHERE email='$email'";
    $res_email = mysqli_query($conn, $sql_email) or die("Error: " . mysqli_error($conn));

    if (mysqli_num_rows($res_email) == 0) {
        $status = "verified";
        $signin_provider = "google";
        $count = 0;

        $sql = "INSERT INTO customer 
                VALUES (default, '$name', NULL, '$email', NULL, '$signin_provider', NOW(), '$status', 1, NULL, $count)";

        mysqli_query($conn, $sql) or die("Error: " . mysqli_error($conn));

        $id = mysqli_insert_id($conn);

        $_SESSION['success'] = "success";
        $_SESSION['user'] = $id;

        header("Location: ../index.php");
        exit;
    } else {
        $data = mysqli_fetch_assoc($res_email);

        if ($data['active'] == 0) {
            $_SESSION['block'] = "Your account has been blocked";
            header("Location: login.php");
            exit;
        }

        $_SESSION['success'] = "success";
        $_SESSION['user'] = $data['id'];

        header("Location: ../index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Google Login | RestroHub</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/responsive.css">
</head>
<body>

<?php require("./components/header.php"); ?>

<main class="center border-curve-lg shadow">
    <h1 class="heading text-center">Login with Gmail</h1>

    <?php
    if (isset($_SESSION["invalid"])) {
        echo '<p class="error-container error p_7-20">' . $_SESSION["invalid"] . '</p>';
        unset($_SESSION["invalid"]);
    }
    ?>

    <form action="./google_auth.php" method="post">

        <div class="text_field">
            <input type="text" class="no_bg no_outline" name="name" placeholder="Your Name" required autofocus>
            <label>Name</label>
        </div>

        <div class="text_field">
            <input type="email" class="no_bg no_outline" name="email" placeholder="yourgmail@gmail.com" required>
            <label>Gmail</label>
        </div>

        <div>
            <input type="submit" class="button w-full h-40 no_outline border-curve-lg mt-20" value="Continue with Gmail">
        </div>

    </form>
</main>

</body>
</html>