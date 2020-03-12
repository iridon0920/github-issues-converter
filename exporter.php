<?php

namespace App;

require 'vendor/autoload.php';
require 'common.php';

echo "エクスポートするissueの状態を入力してください。 o(open) c(closed) other(all)\n";
$input = trim(fgets(STDIN));
if ($input === "o") {
    $state = "open";
} elseif ($input === "c") {
    $state = "closed";
} else {
    $state = "all";
}
$result = [];
$i = 1;
while (1) {
    $github_connection = new Connection(
        getenv('GITHUB_TOKEN'),
        getenv('GITHUB_REPOSITORY'),
        '/issues?page=' . $i . '&state=' . $state
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
    fputcsv($csv, ["id", "タイトル", "内容", "状態", "作成日", "クローズ日"]);
    foreach ($result as $val) {
        fputcsv($csv, [$val->number, $val->title, $val->body, $val->state, $val->created_at, $val->closed_at]);
    }
    echo "CSVファイルの書き出しに成功しました。\n";
} else {
    echo "CSVファイルの書き出しに失敗しました。\n";
}
