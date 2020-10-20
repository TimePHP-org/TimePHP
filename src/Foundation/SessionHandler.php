<?php

namespace TimePHP\Foundation;

use TimePHP\Exception\SessionHandlerException;

class SessionHandler
{

   /**
    * Return a value from $_SESSION for a specific index
    *
    * @param string $index
    * @return mixed|null
    */
   public function get(string $index = null)
   {
      if ($index === null) {
         $list = [];
         foreach ($_SESSION as $key => $value) {
            if (filter_var($value, FILTER_VALIDATE_INT)) $list[$key] = (int)$value;
            elseif (filter_var($value, FILTER_VALIDATE_FLOAT)) $list[$key] = (float)$value;
            elseif (filter_var($value, FILTER_VALIDATE_BOOLEAN)) $list[$key] = (bool)$value;
            else $list[$key] = $value;
         }
         return $list;
      } else {
         if (array_key_exists($index, $_SESSION)) {
            if (filter_var($_SESSION[$index], FILTER_VALIDATE_INT)) return (int)$_SESSION[$index];
            elseif (filter_var($_SESSION[$index], FILTER_VALIDATE_FLOAT)) return (float)$_SESSION[$index];
            elseif (filter_var($_SESSION[$index], FILTER_VALIDATE_BOOLEAN)) return (bool)$_SESSION[$index];
            else return $_SESSION[$index];
         } else {
            return null;
         }
      }
   }

   /**
    * Set a $_SESSION value for a specifid index
    *
    * @param mixed $index
    * @param mixed $object
    * @return self
    */
   public function set(string $index, $object): self
   {
      if($object === null || empty($object)){
         throw new SessionHandlerException("set function must have 1 parameter", 7001);
      } else {
         $_SESSION[$index] = $object;
         return $this;
      }
   }
}
