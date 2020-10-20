<?php

namespace TimePHP\Foundation\Twig;

use TimePHP\UrlParser\Parser;
use TimePHP\Foundation\SessionHandler;

abstract class TwigFeature
{
   /**
    * Request handler for get and post variables
    *
    * @var Parser
    */
   protected $request;

   /**
    * Sesison handler
    *
    * @var SessionHandler
    */
   protected $session;

   public function __construct()
   {
      $this->request = new Parser();
      $this->session = new SessionHandler();
   }
}
