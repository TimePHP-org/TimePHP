<?php

namespace TimePHP\Foundation\Twig;

use Twig\Markup;
use function dump;
use TimePHP\Foundation\Router;
use TimePHP\Foundation\Twig\TwigFeature;

class FunctionTwig extends TwigFeature
{

   public function __construct()
   {
      parent::__construct();
   }

      public function asset($asset) {
         return sprintf('/assets/%s', ltrim($asset, '/'));
      }

      public function component($component): string {
         return sprintf('components/%s', ltrim($component, '/'));
      }

      public function generate(string $nameUrl, array $params = [], array $flags = []): string {
         return sprintf(Router::generate($nameUrl, $params, $flags));
      }

      public function provideCsrf(string $csrfInputName = "csrf_token"): string {
         return !empty($_SESSION) ? new Markup("<input type=\"hidden\" name=\"$csrfInputName\" value=\"{$_SESSION["csrf_token"]}\"/>", "utf-8") : "";
      }

      public function dump($object): string {
         ob_start();
         dump($object);
         return ob_get_clean();
      }

      public function get(string $param) {
         return $this->request->get($param) !== null ? $this->request->get($param) : null;
      }

      public function isConnected() {
         return $this->session->get("csrf_token") !== null;
      }

      public function isAdmin() {
         return $this->session->get("csrf_token") !== null && $this->session->get("user")->role === "admin";
      }

      public function isUser() {
         return $this->session->get("csrf_token") !== null && $this->session->get("user")->role === "user";
      }
   
}
