<?php

namespace TimePHP\Exception;

class RedirectionException extends \Exception {
   
   public function __construct($message = null, $code = 2000) {
      if (!$message) {
         throw new $this('Unknown ' . get_class($this));
      }
      parent::__construct($message, $code);
   }

   public function __toString(): string {
      return __CLASS__ . "[{$this->code}] : {$this->message} at line {$this->line}";
   }
}