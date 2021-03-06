<?php

namespace App;

use RuntimeException;
use stdClass;

class ExpoertCsv
{
    private $Csv;

    public function open($file_name)
    {
        $this->Csv = fopen($file_name, "w");
        if (!$this->Csv) {
            throw new RuntimeException("CSVファイルを開けませんでした。");
        }
    }

    public function writeIssues(array $issues) : bool
    {
        if (!$this->writeHeader()) {
            return false;
        }
        foreach ($issues as $issue) {
            if (!$this->writeBody($issue)) {
                return false;
            }
        }
        return true;
    }

    private function writeHeader() : bool
    {
        return fputcsv($this->Csv, ["id", "タイトル", "内容", "状態", "担当者", "ラベル", "作成日", "クローズ日"]);
    }

    private function writeBody(stdClass $issue) : bool
    {
        // プルリクエストは除外
        if (!$issue->pull_request) {
            if (!fputcsv($this->Csv, $this->createBody($issue))) {
                return false;
            }
        }
        return true;
    }

    private function createBody(stdClass $issue) : array
    {
        return [
            $issue->number,
            $issue->title,
            $issue->body,
            $issue->state,
            $this->getAssigneeNames($issue->assignees),
            $this->getLabelNames($issue->labels),
            $issue->created_at,
            $issue->closed_at
        ];
    }

    private function getAssigneeNames(array $assignees) : string
    {
        $names = '';
        foreach ($assignees as $assignee) {
            $names .= $assignee->login . "\n";
        }
        return $names;
    }

    private function getLabelNames(array $labels) : string
    {
        $names = '';
        foreach ($labels as $label) {
            $names .= $label->name . "\n";
        }
        return $names;
    }
}