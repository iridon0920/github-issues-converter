<?php

namespace App;

use stdClass;

class Connection
{
    private $Con;
    private $Option;

    public function __construct(string $token, string $repository, string $query = '')
    {
        $url = 'https://api.github.com/repos/' . $repository . $query;
        $this->Con = curl_init($url);
        $header = [
            'Authorization: token ' . $token,
            'User-Agent: github-issues-importer-by-csv'
        ];
        $this->Option = [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ];
    }

    public function import(string $title, string $body, string $assignee, array $labels)
    {
        $this->Option[CURLOPT_CUSTOMREQUEST] = 'POST';

        $payload = [
            'title' => $title,
            'body' => $body,
            'assignees' => [$assignee],
            'labels' => $labels
        ];
        $this->Option[CURLOPT_POSTFIELDS] = json_encode($payload); 

        curl_setopt_array($this->Con, $this->Option);
        return curl_exec($this->Con);
    }

    public function export() : array
    {
        $this->Option[CURLOPT_CUSTOMREQUEST] = 'GET';
        curl_setopt_array($this->Con, $this->Option);
        $result = curl_exec($this->Con);
        return json_decode($result);
    }

    public function close() : void
    {
        curl_close($this->Con);
    }

}