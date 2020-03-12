<?php

namespace App;

class Connection
{
    private $Con;
    private $Header;

    public function __construct(string $token, string $repository)
    {
        $url = 'https://api.github.com/repos/' . $repository . '/issues';
        $this->Con = curl_init($url);
        $this->Header = [
            'Authorization: token ' . $token,
            'User-Agent: github-issues-importer-by-csv'
        ];
    }

    public function import(string $title, string $body, string $assignee, array $labels)
    {
        $payload = [
            'title' => $title,
            'body' => $body,
            'assignees' => [$assignee],
            'labels' => $labels
        ];

        $option = [
            CURLOPT_HTTPHEADER => $this->Header,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ];

        curl_setopt_array($this->Con, $option);
        $result = curl_exec($this->Con);
        
        return $result;
    }

    public function close() : void
    {
        curl_close($this->Con);
    }

}