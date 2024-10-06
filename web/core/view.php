<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class view {

    private $_css;
    private $_file;
    private $_title;
    private $_header;
    private $_model;

    public function __construct($page)
    {
        $this->_file = "views/" . $page . "View.php";
        $this->_model = new model();
        $data = $this->_model->extract("header.json");
        $this->_title = ucfirst($data['menu'][$page]);
        $this->_header = $this->generateFile("views/layout/header.php", array("title" => $page, "data" => $data));
        if (file_exists('public/css/' . $page . '.css')) {
            $this->_css = '<link rel="stylesheet" href="' . URL . 'public/css/' . $page . '.css">';
        }
    }

    private function generateFile($file, array $data)
    {
        if (file_exists($file)) {
            if (!empty($data)) {
                extract($data);
            }
            ob_start();
            require_once($file);
            return ob_get_clean();
        } else {
            throw new Exception(404);
        }
    }

    public function buildUp(array $data) {
        $content = $this->generateFile($this->_file, $data);
        $view = $this->generateFile("template.php", array("content" => $content, "header" => $this->_header, "css" => $this->_css, "title" => $this->_title, "footer" => ""));
        echo $view;
    }
}