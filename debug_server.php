<?php
// TEMPORARY DEBUG FILE - DELETE AFTER USE
header('Content-Type: text/plain');
echo "=== SERVER VARIABLES ===\n\n";
echo "REQUEST_URI:     " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SCRIPT_NAME:     " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "PHP_SELF:        " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT:   " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "REQUEST_METHOD:  " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "REDIRECT_STATUS: " . ($_SERVER['REDIRECT_STATUS'] ?? 'N/A') . "\n";
echo "REDIRECT_URL:    " . ($_SERVER['REDIRECT_URL'] ?? 'N/A') . "\n";
echo "\n=== PHP INFO ===\n";
echo "PHP Version:     " . phpversion() . "\n";
echo "__DIR__:         " . __DIR__ . "\n";
echo "getcwd():        " . getcwd() . "\n";
