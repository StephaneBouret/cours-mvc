<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Core\DbConnect;

abstract class Model extends DbConnect 
{
    protected PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = self::getConnection();
    }
}