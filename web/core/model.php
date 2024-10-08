<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class model
{

    private $_pdo;

    public function __construct()
    {
        $conf = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/data/config.json'), true);
        try {
            $this->_pdo = new PDO(
                "mysql:host=" . $conf['hostname'] . ";dbname=" . $conf['database'] . ";port=3306",
                $conf['username'],
                $conf['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Fetch as associative array
                ]
            );
        } catch (PDOException $e) {
?>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body,
                html {
                    height: 100%;
                    font-family: Arial, sans-serif;
                }

                .error-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #f8d7da;
                    color: #721c24;
                }

                .error-box {
                    text-align: center;
                    padding: 20px;
                    border: 2px solid #f5c6cb;
                    border-radius: 10px;
                    background-color: white;
                    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
                }

                .error-box h1 {
                    font-size: 2.5em;
                    margin-bottom: 10px;
                }

                .error-box p {
                    font-size: 1.2em;
                }
            </style>
            <div class="error-container">
                <div class="error-box">
                    <h1><?= ERROR['base'] ?></h1>
                    <p><?= ERROR['database']['ready'] ?><br><?= ERROR['wait'] ?></p>
                </div>
            </div>
            <script>
                setInterval(() => {
                    window.location = "<?= URL ?>";
                }, 3000);
            </script>

<?php
            exit();
        }
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

    public function executeQuery(string $sql, array $params = []): ?array
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
