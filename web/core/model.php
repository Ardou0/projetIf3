<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class model
{

    private $_pdo;

    public function __construct()
    {
        $conf = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/data/config.json'), true);
        $this->_pdo = new PDO("mysql:host=" . $conf['hostname'] . ";dbname=" . $conf['database'] . ";port=3306", $conf['username'], $conf['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch as associative array
        ]);
    }

    public function extract($file)
    {
        $path = PATH . "data/" . LANG . "/" . $file;
        if (!file_exists($path)) {
            throw new Exception(404);
        } else {
            $data = json_decode(file_get_contents("data/" . LANG . $file), true);
            return $data;
        }
    }

    public function readDir($dir)
    {
        $path = PATH . "data/" . LANG . $dir . "/";
        if (!file_exists($path)) {
            throw new Exception(404);
        } else {
            $files = preg_grep('/^([^.])/', scandir($path));
            $projects = [];
            foreach ($files as $project) {
                array_push($projects, $this->extract($dir . "/" . $project));
            }
            return $projects;
        }
    }

    function executeQuery(string $sql, array $params = []): ?array
    {
        $stmt = $this->_pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return !empty($result) ? $result : null;
        } else {
            throw new Exception("Query execution failed.");
        }
        /*
            $sql = "SELECT * FROM destination WHERE country = :country";
            $params = [':country' => 'France'];        
        */
    }
}
