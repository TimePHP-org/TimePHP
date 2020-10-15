<?php

namespace TimePHP\Security;

class PasswordService {

   /**
    * Hash a password using the Bcrypt default algorithm
    *
    * @param string $password
    * @param string $algo
    * @return string
    */
   public static function hash(string $password, string $algo = PASSWORD_BCRYPT): string{
      return password_hash($password, $algo);
   }

   /**
    * Compare a password and a hash
    *
    * @param string $password
    * @param string $hash
    * @return boolean
    */
   public static function compare(string $password, string $hash): bool {
      return password_verify($password, $hash);
   }
   
}