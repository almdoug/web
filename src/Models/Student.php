<?php

namespace Dougl\Projetoweb\Models;

class Student {
    public int $id;
    public string $registration;
    public string $name;
    public string $email;
    public string $course;
    public string $bio;
    public array $skills = [];
}