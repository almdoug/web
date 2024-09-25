<?php

namespace Dougl\Projetoweb\Models;

class Project {
    public int $id;
    public string $title;
    public string $description;
    public string $type;
    public string $start_date;
    public string $end_date;
    public int $professor_id;
}