<?php
echo "1. Resetting Database...\n";
passthru('php setup_db.php');
echo "\n2. Running Tests...\n";
passthru('php tests/library_test.php');
