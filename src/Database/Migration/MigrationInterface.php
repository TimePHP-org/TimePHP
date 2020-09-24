<?php

namespace TimePHP\Database\Migration;

interface MigrationInterface {
   public function up();
   public function down();
}
