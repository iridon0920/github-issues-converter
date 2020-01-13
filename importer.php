<?php

namespace App;
use Dotenv\Dotenv;
use SplFileObject;

require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class IssuesImporter
{
    private $Token;
    private $Url;

    public function __construct(string $token, string $repository)
    {
        $this->Token = $token;
        $this->Url = 'https://api.github.com/repos/' . $repository . '/issues';
    }

    public function import(string $title, string $body, string $assignee, array $labels)
    {
        $ch = curl_init($this->Url);
        $payload = [
            'title' => $title,
            'body' => $body,
            'assignees' => [$assignee],
            'labels' => $labels
        ];

        $header = [
            'Authorization: token ' . $this->Token,
            'User-Agent: github-issues-importer-by-csv'
        ];

        $option = [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ];

        curl_setopt_array($ch, $option);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

$issues_importer = new IssuesImporter(
    getenv('GITHUB_TOKEN'),
    getenv('GITHUB_REPOSITORY')
);

$file_name = trim(fgets(STDIN));
if ($file_name) {
    $csv_file = new SplFileObject($file_name);
    while (!$csv_file->eof()) {
        $arr = $csv_file->fgetcsv();
        if (!is_null($arr)) {
            $labels = [];
            for ($i = 3; $i <= count($arr) - 1; $i++) {
                $labels[] = $arr[$i];
            }
            $issues_importer->import($arr[0], $arr[1], $arr[2], $labels);
        }
    }
}

