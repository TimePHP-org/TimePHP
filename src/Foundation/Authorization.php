<?php

namespace TimePHP\Foundation;

use TimePHP\Foundation\SessionHandler;

class Authorization
{

   /**
    * session handler
    *
    * @var SessionHandler
    */
   private $session;
   

   public function __construct()
   {
      $this->session = new SessionHandler();
   }

   /**
    * Check if the current user is admin
    *
    * @return boolean
    */
   public function isAdmin(): bool
   {
      if ($this->session->get("user")->role === "admin") return true;
      else return false;
   }

   /**
    * Check if the current user is a user
    *
    * @return boolean
    */
   public function isUser(): bool
   {
      if ($this->session->get("user")->role === "user") return true;
      else return false;
   }

   /**
    * Check if the current user is a user
    *
    * @return boolean
    */
   public function isConnected(): bool
   {
      if ($this->session->get("csrf_token") !== null) return true;
      else return false;
   }
}
