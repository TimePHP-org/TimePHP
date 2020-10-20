<?php

namespace TimePHP\Foundation\Twig;

class FunctionTwig {

   public function asset($asset): string {
      return sprintf('/assets/%s', ltrim($asset, '/'));
   }

}