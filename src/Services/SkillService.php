<?php

namespace Dougl\Projetoweb\Services;

use Dougl\Projetoweb\Models\Skill;

class SkillService {
    private $dataFile = __DIR__ . '/../../data/skills.json';

    public function __construct() {
        if (!file_exists($this->dataFile) || filesize($this->dataFile) == 0) {
            $this->initializeDataFile();
        }
    }

    private function initializeDataFile() {
        $initialData = ['nextId' => 1, 'skills' => []];
        file_put_contents($this->dataFile, json_encode($initialData));
    }

    private function readData() {
        $jsonData = file_get_contents($this->dataFile);
        $data = json_decode($jsonData, true);
        
        if (!isset($data['nextId']) || !isset($data['skills'])) {
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
        error_log('Habilidades armazenadas: ' . print_r($data['skills'], true));
        return array_values($data['skills']);
    }

    public function getById($id) {
        $data = $this->readData();
        return $data['skills'][$id] ?? null;
    }

    public function create(Skill $skill) {
        $data = $this->readData();
        $id = $data['nextId']++;
        $skill->id = $id;
        $data['skills'][$id] = $skill;
        $this->writeData($data);
        error_log('Habilidade criada: ' . print_r($skill, true));
        return $skill;
    }

    public function update($id, Skill $skill) {
        $data = $this->readData();
        if (!isset($data['skills'][$id])) {
            return null;
        }
        $skill->id = $id;
        $data['skills'][$id] = $skill;
        $this->writeData($data);
        return $skill;
    }

    public function delete($id) {
        $data = $this->readData();
        if (!isset($data['skills'][$id])) {
            return false;
        }
        unset($data['skills'][$id]);
        $this->writeData($data);
        return true;
    }
}