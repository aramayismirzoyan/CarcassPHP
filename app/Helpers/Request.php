<?php

namespace App\Helpers;

class Request
{
    private readonly array $jsonData;
    public function __construct()
    {
        $this->setJsonData();
    }

    private function getInputData()
    {
        $json = file_get_contents('php://input');

        if ($json !== '') {
            return json_decode($json, true);
        }

        return [];
    }

    private function setJsonData()
    {
        $this->jsonData = $this->getInputData();
    }

    public function getParam($data, $key)
    {
        return $data[$key] ?? '';
    }

    public function getJsonData(): array
    {
        return $this->jsonData;
    }

    public function isValidType($type): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $type;
    }
}
