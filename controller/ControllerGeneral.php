<?php
class ControllerGeneral extends Controller {
    public function __construct(AltoRouter $router) {
        parent::__construct($router);
    }
    public function contact() {
        require_once './view/contact.php';
    }
}