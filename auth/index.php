<?php
require_once 'app/config.php';

require_once 'app/api_request.php';

if(empty($_GET['action'])) {
    require_once 'views/home.html';
} elseif($_GET['action'] == 'login') {
    require_once 'app/handle_login.php';
}



