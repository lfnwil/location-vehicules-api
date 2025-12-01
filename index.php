<?php
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use function Config\getDatabase;

$db = getDatabase();

require __DIR__ . '/Routes/indexRoutes.php';