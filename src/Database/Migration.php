<?php

namespace TimePHP\Database;

require __DIR__ . "/../../../../autoload.php";

use TimePHP\Database\Migration\MigrationInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Symfony\Component\Dotenv\Dotenv;

abstract class Migration implements MigrationInterface {
   /**
    * Dotenv variable
    *
    * @var Dotenv
    */
   protected $dotenv;

   /**
    * Eloquent capsule variable
    *
    * @var Capsule
    */
   protected $capsule;

   public function __construct() {
      $this->dotenv = new Dotenv();
      $this->dotenv->load(__DIR__ . '/../../../../../config/.env');

      $this->capsule = new Capsule;
      $this->capsule->addConnection([
         'driver' => 'mysql',
         'host' => $_ENV['DB_HOST'],
         'database' => $_ENV['DB_NAME'],
         'username' => $_ENV['DB_USER'],
         'password' => $_ENV['DB_PASS'],
         'port' => $_ENV['DB_PORT'],
         'charset' => 'utf8',
         'collation' => 'utf8_unicode_ci',
         'prefix' => '',
      ]);
      $this->capsule->setEventDispatcher(new Dispatcher(new Container));
      $this->capsule->setAsGlobal();
      $this->capsule->bootEloquent();
   }
}
