<?php
require_once 'app/config.php';

setcookie("login_type", getenv('LOGIN_TYPE'), time() + 3600, "/");

if (empty($_GET['action'])) {
    require_once 'views/home.html';
} elseif ($_GET['action'] == 'login') {
    require_once 'app/handle_login.php';
}

if (isset($_GET['code'])) {
    require_once 'login.php';
}

require_once 'app/api_request.php';

if (!empty($_SESSION['jwt_token'])) {
    require_once 'app/submit_form.php';
}
