<?php
if (!isset($_COOKIE['logged_in']) && $_COOKIE['logged_in'] != 1) {
    header("Location: login.php");
    exit;
}

$email = false;
if (isset($_COOKIE['email']) && $_COOKIE['email'] != '') {
    $email = filter_input(INPUT_COOKIE, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Frontend testing examples!</title>
    <script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#do_ajax").on('click', function (event) {
            if ($("#enable_ajax").is(":checked")) {
                $.get(
                    'ajax.php',
                    function (data) {
                        $("#ajax_result").text(data.response);
                    },
                    "json"
                );
            }
            event.preventDefault();
        });

        $("#clear_ajax").on('click', function (event) {
            $("#ajax_result").text('');
            event.preventDefault();
        });
    });
    </script>
</head>
<body>

<p>Hello <?php echo $email ? $email : "Anon" ?>.</p>

<label for="enable_ajax"><input type="checkbox" name="enable_ajax" id="enable_ajax"> Enable ajax?</label>

<a id="do_ajax" href="#">Do an ajax request</a>

<div>Result of an ajax request will go here: <span id="ajax_result"></span><div>

<a id="clear_ajax" href="#">Clear ajax response</a>

</body>
</html>
