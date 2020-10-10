<?php

namespace TimePHP\Security;

class CsrfToken {
 
   /**
    * Generate a valid CSRF token
    *
    * @return string
    */
   public static function generate(): string {
      return bin2hex(random_bytes(32));
   }

   /**
    * Compare 2 hashes
    *
    * @param string $sessionToken
    * @param string $inputToken
    * @return boolean
    */
   public static function compare(string $sessionToken, string $inputToken): bool {
      return hash_equals($sessionToken, $inputToken);
   }
   
}