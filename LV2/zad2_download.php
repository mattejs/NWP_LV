<?php
    session_start();

    $db = new mysqli("localhost", "test", "test", "radovi");
	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	}
    
    function decrypt($file){
        $decryption_key = md5('k3y');
        $cipher = 'AES-128-CTR';
        $options = 0;
        $decryption_iv = $_SESSION['iv'];
        $content = file_get_contents("$file");
        $data = base64_decode($content);
        $data = openssl_decrypt($data , $cipher,
        $decryption_key, $options , $decryption_iv );
        return($data);
    }    

    $targetDir = "uploads/";

    $q = "SELECT DISTINCT * FROM testbase";
    $r2 = $db->query($q);

    if ($r2->num_rows > 0) {
        while($item = $r2->fetch_assoc()) {
            file_put_contents($targetFilePath, decrypt($targetDir . $item["data"]));
            echo '<a href="'.$targetDir . $item["data"].'" download>' . $item["data"] . '</a><br>';
        }
}