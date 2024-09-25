<?php

namespace Dougl\Projetoweb\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dougl\Projetoweb\Services\SkillService;
use Dougl\Projetoweb\Models\Skill;

class SkillController {
    private $skillService;

    public function __construct() {
        $this->skillService = new SkillService();
    }

    public function getAll(Request $request, Response $response) {
        $skills = $this->skillService->getAll();
        $response->getBody()->write(json_encode($skills));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getById(Request $request, Response $response, $args) {
        $skill = $this->skillService->getById($args['id']);
        if ($skill) {
            $response->getBody()->write(json_encode($skill));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404);
    }

    public function create(Request $request, Response $response) {
        $data = json_decode($request->getBody()->getContents(), true);
        error_log('Dados recebidos (Skill): ' . print_r($data, true));
        
        $requiredFields = ['title', 'description'];
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
        
        $skill = new Skill();
        $skill->title = $data['title'];
        $skill->description = $data['description'];
        
        try {
            $newSkill = $this->skillService->create($skill);
            $response->getBody()->write(json_encode($newSkill));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, $args) {
        $data = json_decode($request->getBody()->getContents(), true);

        $requiredFields = ['title', 'description'];
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
        
        $skill = new Skill();
        $skill->title = $data['title'];
        $skill->description = $data['description'];
        $updatedSkill = $this->skillService->update($args['id'], $skill);
        if ($updatedSkill) {
            $response->getBody()->write(json_encode($updatedSkill));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404);
    }

    public function delete(Request $request, Response $response, $args) {
        $result = $this->skillService->delete($args['id']);
        if ($result) {
            return $response->withStatus(204);
        }
        return $response->withStatus(404);
    }
}