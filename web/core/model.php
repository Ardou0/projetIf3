<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class model {

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
            foreach($files as $project) {
                array_push($projects, $this->extract($dir."/".$project));
            }
            return $projects;
        }
    }

}