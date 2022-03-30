<?php
	session_start();

	$db = new mysqli("localhost", "test", "test", "radovi");
	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	}

	function encrypt($someFile){
		$encryption_key = md5('k3y');
		$data = file_get_contents("$someFile");
		$cipher = 'AES-128-CTR';
		$iv_length = openssl_cipher_iv_length($cipher);
		$options = 0;
		$encryption_iv = random_bytes($iv_length);
		$data = openssl_encrypt($data , $cipher, $encryption_key, $options , $encryption_iv );
		$_SESSION['podaci'] = base64_encode($data);
		$_SESSION['iv'] = $encryption_iv;
		return base64_encode($data);
	}
	
	$targetDir = "uploads/";
	$fileName = basename($_FILES["file"]["name"]);
	$targetFilePath = $targetDir . $fileName;
	$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
	if(isset($_POST["submit"]) && !empty($_FILES["file"]["name"])){
		$allowTypes = array('jpg','png','jpeg','pdf');
		if(in_array($fileType, $allowTypes)){            
			if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){  				
				file_put_contents($targetFilePath, encrypt($targetFilePath));
				$insert = $db->query("INSERT INTO testbase (data) VALUES ('$fileName')");
				if($insert){
					$statusMsg = "Upload successful";                    
				}
                else{
					$statusMsg = "Failed";
				} 
			}
            else{
				$statusMsg = "Error";
			}
		}
        else{
			$statusMsg = 'Invalid file format';
		}
	}
    else{
		$statusMsg = 'Select a file.';
	}
	echo $statusMsg;
?>