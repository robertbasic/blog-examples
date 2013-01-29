<?php
if (count($_POST) and isset($_POST['login'])) {
    setcookie('logged_in', 1, time() + 3600, '/');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setcookie('email', $email, time() + 3600, '/');
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Frontend testing examples! Login page.</title>
</head>
<body>
    <form name="login_form" id="login_form" method="POST">
        <input type="text" name="email" id="email" placeholder="Set an email!">
        <input type="submit" name="login" id="login" value="Log in!">
    </form>
</body>
</html>
