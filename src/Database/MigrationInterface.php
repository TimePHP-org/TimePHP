<?php

namespace TimePHP\Database;

interface MigrationInterface {
   public function up();
   public function down();
}
