<?php

namespace Dougl\Projetoweb\Services;

use Dougl\Projetoweb\Models\Professor;

class ProfessorService {
    private $dataFile = __DIR__ . '/../../data/professors.json';

    public function __construct() {
        if (!file_exists($this->dataFile) || filesize($this->dataFile) == 0) {
            $this->initializeDataFile();
        }
    }

    private function initializeDataFile() {
        $initialData = ['nextId' => 1, 'professors' => []];
        file_put_contents($this->dataFile, json_encode($initialData));
    }

    private function readData() {
        $jsonData = file_get_contents($this->dataFile);
        $data = json_decode($jsonData, true);
        
        if (!isset($data['nextId']) || !isset($data['professors'])) {
            $this->initializeDataFile();
            return $this->readData();
        }
        
        return $data;
    }

    private function writeData($data) {
        file_put_contents($this->dataFile, json_encode($data));
    }

    public function getAll() {
        $data = $this->readData();
        error_log('Professores armazenados: ' . print_r($data['professors'], true));
        return array_values($data['professors']);
    }

    public function getById($id) {
        $data = $this->readData();
        return $data['professors'][$id] ?? null;
    }

    public function create(Professor $professor) {
        $data = $this->readData();
        $id = $data['nextId']++;
        $professor->id = $id;
        $data['professors'][$id] = $professor;
        $this->writeData($data);
        error_log('Professor criado: ' . print_r($professor, true));
        return $professor;
    }

    public function update($id, Professor $professor) {
        $data = $this->readData();
        if (!isset($data['professors'][$id])) {
            return null;
        }
        $professor->id = $id;
        $data['professors'][$id] = $professor;
        $this->writeData($data);
        return $professor;
    }

    public function delete($id) {
        $data = $this->readData();
        if (!isset($data['professors'][$id])) {
            return false;
        }
        unset($data['professors'][$id]);
        $this->writeData($data);
        return true;
    }
}