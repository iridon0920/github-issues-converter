<?php

namespace App;

require 'vendor/autoload.php';
require 'common.php';

$result = [];
$i = 1;
while (1) {
    $github_connection = new Connection(
        getenv('GITHUB_TOKEN'),
        getenv('GITHUB_REPOSITORY'),
        '/issues?page=' . $i
    );
    $issues = $github_connection->export();
    $result = array_merge($result, $issues); 
    $github_connection->close();
    if (!$issues) {
        break;
    }
    $i++;
}
echo count($result);