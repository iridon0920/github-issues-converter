<?php

namespace App;
use SplFileObject;

require 'vendor/autoload.php';
require 'common.php';

$github_connection = new Connection(
    getenv('GITHUB_TOKEN'),
    getenv('GITHUB_REPOSITORY')
);

echo "インポートするCSVファイル名を入力してください。\n";

$file_name = trim(fgets(STDIN));
if ($file_name) {
    $csv_file = new SplFileObject($file_name);
    while (!$csv_file->eof()) {
        $arr = $csv_file->fgetcsv();
        if (!is_null($arr)) {
            $labels = [];
            for ($i = 3; $i <= count($arr) - 1; $i++) {
                if ($arr[$i]) {
                    $labels[] = $arr[$i];
                }
            }
            $result = $github_connection->import($arr[0], $arr[1], $arr[2], $labels);
            if ($result) {
                echo $arr[0] . " is import OK\n";
            } else {
                echo $arr[0] . " is import NG\n";
                exit;
            }
        }
    }
}
$github_connection->close();
