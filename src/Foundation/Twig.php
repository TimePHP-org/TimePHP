<?php

namespace TimePHP\Foundation;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

class Twig {


   /**
    * Twig variable
    *
    * @var Environment
    */
   private $twig;

   /**
    * array of custom functions
    *
    * @param array $functions
    */
   public function __construct(array $functions){
            
      $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../../../../". $_ENV['VIEW_PATH']));

      $this->twig->addFunction(new TwigFunction('asset', function ($asset): string{
         return sprintf('/assets/%s', ltrim($asset, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('component', function ($component): string{
         return sprintf('components/%s', ltrim($component, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('generate', function (string $nameUrl, array $params = [], array $flags = []): string{
         return sprintf(Router::generate($nameUrl, $params, $flags));
      }));

      $this->twig->addFunction(new TwigFunction('dump', function ($object): string
      {
         ob_start();
         dump($object);
         return ob_get_clean();
      }));

      foreach($functions["twig"] as $function){
         $this->twig->addFunction(new TwigFunction($function["name"], $function["function"]));
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