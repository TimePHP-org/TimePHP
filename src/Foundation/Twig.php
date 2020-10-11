<?php

namespace TimePHP\Foundation;

use Closure;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use TimePHP\Exception\TwigException;

class Twig {

   /**
    * Twig variable
    *
    * @var Environment
    */
   private $twig;

   /**
    * array of custom options
    *
    * @param array $options
    */
   public function __construct(array $options) {

      $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../../../../App/Bundle/Views"));

      $this->twig->addFunction(new TwigFunction('asset', function ($asset): string {
         return sprintf('/assets/%s', ltrim($asset, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('component', function ($component): string {
         return sprintf('components/%s', ltrim($component, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('generate', function (string $nameUrl, array $params = [], array $flags = []): string {
         return sprintf(Router::generate($nameUrl, $params, $flags));
      }));

      $this->twig->addFunction(new TwigFunction('dump', function ($object): string {
         ob_start();
         dump($object);
         return ob_get_clean();
      }));

      foreach ($options["twig"] as $function) {

         $name = $function["name"];

         if (array_key_exists("function", $function) && is_object($function["function"])) {
            if($function["type"] === "function") {
               $this->twig->addFunction(new TwigFunction($name, $function["function"]));
            } elseif($function["type"] === "filter") {
               $this->twig->addFilter(new TwigFilter($name, $function["function"]));
            }
         } else if (array_key_exists("class", $function) && array_key_exists("function", $function) && is_callable([new $function["class"], $function["function"]])) {
            $callable = Closure::fromCallable([new $function["class"], $function["function"]]);
            if($function["type"] === "function") {
               $this->twig->addFunction(new TwigFunction($name, $callable));
            } elseif($function["type"] === "filter") {
               $this->twig->addFilter(new TwigFilter($name, $callable));
            }
         } else {
            if ($_ENV["APP_ENV"] == 0) {
               header('HTTP/1.1 500 Internal Server Error');
            } else {
               if ($type === "addFunction") {
                  throw new TwigException("Cannot add the custom twig function : $name", 4001);
               } elseif ($type === "addFilter") {
                  throw new TwigException("Cannot add the custom twig filter : $name", 4001);
               }
            }
         }
      }

   }

   /**
    * Return Twig variable
    *
    * @return Environment
    */
   public function getRenderer(): Environment {
      return $this->twig;
   }

}