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

$csv = fopen("export.csv", "w");
if ($csv) {
    fputcsv($csv, ["id", "タイトル", "内容", "作成日", "クローズ日"]);
    foreach ($result as $val) {
        fputcsv($csv, [$val->number, $val->title, $val->body, $val->created_at, $val->closed_at]);
    }
    echo "CSVファイルの書き出しに成功しました。\n";
} else {
    echo "CSVファイルの書き出しに失敗しました。\n";
}
