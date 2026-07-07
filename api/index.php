<?php
// Fix Vercel's subdirectory routing issue by resetting script names
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Forward Vercel requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
