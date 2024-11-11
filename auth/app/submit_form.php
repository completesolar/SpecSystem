<?php

require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

$specUrl = getenv('SPEC_LOGIN_URL');
$jwtParts = explode(".", $_SESSION['jwt_token']);
$payload = json_decode(base64_decode($jwtParts[1]),true);
$jwtToken = JWT::encode($payload, getenv('SECRET_JWT'), 'HS256');

header('Location: ' . $specUrl . '?' . http_build_query([
    'jwt_token' => $jwtToken,
    'state' => $_SESSION['state'],
]));