<?php 
    require 'vendor/autoload.php';
    use \Firebase\JWT\JWT;
    $secretKey = 'your_secret_key';
?>
<form id="login" action="<?php echo getenv('SPEC_LOGIN_URL');?>" method="get">
    <?php
    foreach ($_SESSION as $a => $b) {
        if($a=="jwt_token"){
            $token = $b;
            $payload = explode(".",$b);
            $payload = json_decode(base64_decode($payload[1]),true);
            $jwtToken = JWT::encode($payload, $secretKey, 'HS256');
            echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($jwtToken).'">';
        }else{
            echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
        }
    }
    ?>
</form>
<script type="text/javascript">
    document.getElementById('login').submit();
</script>
