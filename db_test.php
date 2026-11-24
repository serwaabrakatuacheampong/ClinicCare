<?php

require_once __DIR__ . '/config/database.php';

if (isset($conn) && $conn instanceof mysqli) {
    echo "SUCCESS! Database connection is working.";
} else {
    echo "FAILED! Database connection is not established.";
}

?>