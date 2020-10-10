<?php

namespace TimePHP\Exception;

class SessionException extends Exception {

   public function __construct($message, $code = "1-000") {
      parent::__construct($message, $code);
   }

   // chaîne personnalisée représentant l'objet
   public function __toString() {
      return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
   }
}