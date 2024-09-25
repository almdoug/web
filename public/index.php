<?php

use Slim\Factory\AppFactory;
use Dougl\Projetoweb\Controllers\ProfessorController;
use Dougl\Projetoweb\Controllers\StudentController;
use Dougl\Projetoweb\Controllers\ProjectController;
use Dougl\Projetoweb\Controllers\SkillController;
use Dougl\Projetoweb\Services\ProfessorService;
use Dougl\Projetoweb\Services\StudentService;
use Dougl\Projetoweb\Services\ProjectService;
use Dougl\Projetoweb\Services\SkillService;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Professor routes
$professorService = new ProfessorService();
$professorController = new ProfessorController($professorService);

$app->get('/professors', [$professorController, 'getAll']);
$app->get('/professors/{id}', [$professorController, 'getById']);
$app->post('/professors', [$professorController, 'create']);
$app->put('/professors/{id}', [$professorController, 'update']);
$app->delete('/professors/{id}', [$professorController, 'delete']);

// Student routes
$studentService = new StudentService();
$studentController = new StudentController($studentService);

$app->get('/students', [$studentController, 'getAll']);
$app->get('/students/{id}', [$studentController, 'getById']);
$app->post('/students', [$studentController, 'create']);
$app->put('/students/{id}', [$studentController, 'update']);
$app->delete('/students/{id}', [$studentController, 'delete']);

$app->post('/students/{id}/skills', [$studentController, 'addSkills']);

// Project routes
$projectService = new ProjectService();
$projectController = new ProjectController($projectService);

$app->get('/projects', [$projectController, 'getAll']);
$app->get('/projects/{id}', [$projectController, 'getById']);
$app->post('/projects', [$projectController, 'create']);
$app->put('/projects/{id}', [$projectController, 'update']);
$app->delete('/projects/{id}', [$projectController, 'delete']);

// Skill routes
$skillService = new SkillService();
$skillController = new SkillController($skillService);

$app->get('/skills', [$skillController, 'getAll']);
$app->get('/skills/{id}', [$skillController, 'getById']);
$app->post('/skills', [$skillController, 'create']);
$app->put('/skills/{id}', [$skillController, 'update']);
$app->delete('/skills/{id}', [$skillController, 'delete']);

$app->run();