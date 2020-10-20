<?php

namespace TimePHP\Foundation\Twig;

use DateTime;
use TimePHP\Foundation\Twig\TwigFeature;

class FilterTwig extends TwigFeature
{

   public function __construct()
   {
      parent::__construct();
   }

   public function truncate(string $text, int $length) {
      return substr($text, 0, $length);
   }

   public function formatDate(DateTime $date, string $format) {
      $date = new DateTime($date);
      return $date->format($format);
   }
}
