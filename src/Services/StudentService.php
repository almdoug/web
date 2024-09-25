<?php

namespace Dougl\Projetoweb\Services;

use Dougl\Projetoweb\Models\Student;

class StudentService {
    private $dataFile = __DIR__ . '/../../data/students.json';

    public function __construct() {
        if (!file_exists($this->dataFile) || filesize($this->dataFile) == 0) {
            $this->initializeDataFile();
        }
    }

    private function initializeDataFile() {
        $initialData = ['nextId' => 1, 'students' => []];
        file_put_contents($this->dataFile, json_encode($initialData));
    }

    private function readData() {
        $jsonData = file_get_contents($this->dataFile);
        $data = json_decode($jsonData, true);
        
        if (!isset($data['nextId']) || !isset($data['students'])) {
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
        error_log('Estudantes armazenados: ' . print_r($data['students'], true));
        return array_values($data['students']);
    }

    public function getById($id) {
        $data = $this->readData();
        return $data['students'][$id] ?? null;
    }

    public function create(Student $student) {
        $data = $this->readData();
        $id = $data['nextId']++;
        $student->id = $id;
        $data['students'][$id] = $student;
        $this->writeData($data);
        error_log('Estudante criado: ' . print_r($student, true));
        return $student;
    }

    public function update($id, Student $student) {
        $data = $this->readData();
        if (!isset($data['students'][$id])) {
            return null;
        }
        $student->id = $id;
        $data['students'][$id] = $student;
        $this->writeData($data);
        return $student;
    }

    public function delete($id) {
        $data = $this->readData();
        if (!isset($data['students'][$id])) {
            return false;
        }
        unset($data['students'][$id]);
        $this->writeData($data);
        return true;
    }

    public function addSkillsToStudent($studentId, $skillIds) {
        $data = $this->readData();
        if (!isset($data['students'][$studentId])) {
            return null;
        }

        $student = $data['students'][$studentId];
        
        // Inicialize a propriedade skills se ela não existir
        if (!isset($student['skills']) || !is_array($student['skills'])) {
            $student['skills'] = [];
        }

        // Adicione as novas habilidades
        $student['skills'] = array_unique(array_merge($student['skills'], $skillIds));
        
        $data['students'][$studentId] = $student;
        $this->writeData($data);

        return $this->arrayToStudent($student);
    }

    private function arrayToStudent($array) {
        $student = new Student();
        $student->id = $array['id'];
        $student->name = $array['name'];
        $student->registration = $array['registration'];
        $student->email = $array['email'];
        $student->course = $array['course'];
        $student->bio = $array['bio'];
        $student->skills = $array['skills'] ?? [];
        return $student;
    }
}