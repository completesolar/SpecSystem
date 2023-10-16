<?php
session_start();
$appid = getenv('APP_ID');
$secret = getenv('SECRET');
$login_url = getenv('LOGIN_URL');
$redirect_url = getenv('REDIRECT_URL');
$login_type = getenv('LOGIN_TYPE');
$_SESSION['state'] = session_id();
