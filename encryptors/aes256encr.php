
<?php
     //The encryption
    $key = pack('H*', "yafskhagfaksgfasjkfgskjagfsakjflkjafkjskjlsajfkh");
    
    
    $key_size =  strlen($key); // size of the key
    echo "Key size: " . $key_size . "\n";
    
    $plaintext = "Example of AES-256";

    
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); //the size of initialization vector
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
    
    
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
                                 $plaintext, MCRYPT_MODE_CBC, $iv);

   
    $ciphertext = $iv . $ciphertext;
     
    //The decryption
    $ciphertext_base64 = base64_encode($ciphertext);
    echo "<br>";
    echo  $ciphertext_base64 . "\n";
	
	$ciphertext_dec = base64_decode($ciphertext_base64);
    
    
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
    
    
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
                                    $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    
    echo  $plaintext_dec . "\n";

    
    
   
?>
