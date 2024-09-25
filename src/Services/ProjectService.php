<?php

namespace Dougl\Projetoweb\Services;

use Dougl\Projetoweb\Models\Project;

class ProjectService {
    private $dataFile = __DIR__ . '/../../data/projects.json';

    public function __construct() {
        if (!file_exists($this->dataFile) || filesize($this->dataFile) == 0) {
            $this->initializeDataFile();
        }
    }

    private function initializeDataFile() {
        $initialData = ['nextId' => 1, 'projects' => []];
        file_put_contents($this->dataFile, json_encode($initialData));
    }

    private function readData() {
        $jsonData = file_get_contents($this->dataFile);
        $data = json_decode($jsonData, true);
        
        if (!isset($data['nextId']) || !isset($data['projects'])) {
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
        error_log('Projetos armazenados: ' . print_r($data['projects'], true));
        return array_values($data['projects']);
    }

    public function getById($id) {
        $data = $this->readData();
        return $data['projects'][$id] ?? null;
    }

    public function create(Project $project) {
        $data = $this->readData();
        $id = $data['nextId']++;
        $project->id = $id;
        $data['projects'][$id] = $project;
        $this->writeData($data);
        error_log('Projeto criado: ' . print_r($project, true));
        return $project;
    }

    public function update($id, Project $project) {
        $data = $this->readData();
        if (!isset($data['projects'][$id])) {
            return null;
        }
        $project->id = $id;
        $data['projects'][$id] = $project;
        $this->writeData($data);
        return $project;
    }

    public function delete($id) {
        $data = $this->readData();
        if (!isset($data['projects'][$id])) {
            return false;
        }
        unset($data['projects'][$id]);
        $this->writeData($data);
        return true;
    }
}