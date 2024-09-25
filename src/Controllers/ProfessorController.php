<?php

namespace Dougl\Projetoweb\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dougl\Projetoweb\Services\ProfessorService;
use Dougl\Projetoweb\Models\Professor;

class ProfessorController {
    private $professorService;

    public function __construct() {
        $this->professorService = new ProfessorService();
    }

    public function getAll(Request $request, Response $response) {
        $professors = $this->professorService->getAll();
        $response->getBody()->write(json_encode($professors));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args) {
        $professor = $this->professorService->getById($args['id']);
        if ($professor) {
            $response->getBody()->write(json_encode($professor));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404);
    }

    public function create(Request $request, Response $response) {
        $data = json_decode($request->getBody()->getContents(), true);
        error_log('Dados recebidos (Professor): ' . print_r($data, true));
        
        $requiredFields = ['name', 'email', 'department', 'bio'];
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
        
        $professor = new Professor();
        $professor->name = $data['name'];
        $professor->email = $data['email'];
        $professor->department = $data['department'];
        $professor->bio = $data['bio'];
        
        try {
            $newProfessor = $this->professorService->create($professor);
            $response->getBody()->write(json_encode($newProfessor));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);
        
        $requiredFields = ['name', 'email', 'department', 'bio'];
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
        
        $professor = new Professor();
        $professor->name = $data['name'];
        $professor->email = $data['email'];
        $professor->department = $data['department'];
        $professor->bio = $data['bio'];
        
        try {
            $updatedProfessor = $this->professorService->update($args['id'], $professor);
            if ($updatedProfessor) {
                $response->getBody()->write(json_encode($updatedProfessor));
                return $response->withHeader('Content-Type', 'application/json');
            }
            return $response->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, $args) {
        try {
            $result = $this->professorService->delete($args['id']);
            if ($result) {
                return $response->withStatus(204);
            }
            return $response->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}