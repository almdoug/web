<?php

namespace Dougl\Projetoweb\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dougl\Projetoweb\Services\StudentService;
use Dougl\Projetoweb\Models\Student;

class StudentController {
    private $studentService;

    public function __construct() {
        $this->studentService = new StudentService();
    }

    public function getAll(Request $request, Response $response) {
        $students = $this->studentService->getAll();
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args) {
        $student = $this->studentService->getById($args['id']);
        if ($student) {
            $response->getBody()->write(json_encode($student));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404);
    }

    public function create(Request $request, Response $response) {
        $data = json_decode($request->getBody()->getContents(), true);
        error_log('Dados recebidos (Student): ' . print_r($data, true));
        
        $requiredFields = ['name', 'registration', 'email', 'course', 'bio'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            $errorMessage = 'Os seguintes campos são obrigatórios: ' . implode(', ', $missingFields);
            $response->getBody()->write(json_encode(['error' => $errorMessage]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $student = new Student();
        $student->name = $data['name'];
        $student->registration = $data['registration'];
        $student->email = $data['email'];
        $student->course = $data['course'];
        $student->bio = $data['bio'];
        
        try {
            $newStudent = $this->studentService->create($student);
            $response->getBody()->write(json_encode($newStudent));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);

        $requiredFields = ['registration', 'name', 'email', 'course', 'bio'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            $errorMessage = 'Os seguintes campos são obrigatórios: ' . implode(', ', $missingFields);
            $response->getBody()->write(json_encode(['error' => $errorMessage]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $student = new Student();
        $student->registration = $data['registration'];
        $student->name = $data['name'];
        $student->email = $data['email'];
        $student->course = $data['course'];
        $student->bio = $data['bio'];
        $updatedStudent = $this->studentService->update($args['id'], $student);
        if ($updatedStudent) {
            $response->getBody()->write(json_encode($updatedStudent));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404);
    }

    public function delete(Request $request, Response $response, $args) {
        $result = $this->studentService->delete($args['id']);
        if ($result) {
            return $response->withStatus(204);
        }
        return $response->withStatus(404);
    }

    public function addSkills(Request $request, Response $response, $args) {
        $studentId = $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['skills']) || !is_array($data['skills'])) {
            $response->getBody()->write(json_encode(['error' => 'O campo "skills" é obrigatório e deve ser um array']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $updatedStudent = $this->studentService->addSkillsToStudent($studentId, $data['skills']);
            
            if ($updatedStudent) {
                $response->getBody()->write(json_encode($updatedStudent));
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode(['error' => 'Estudante não encontrado']));
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}