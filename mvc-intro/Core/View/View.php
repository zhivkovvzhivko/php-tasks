<?php
/**
 * Created by PhpStorm.
 * User: Jivko
 * Date: 3/30/2019
 * Time: 3:33 PM
 */

namespace Core\View;


class View implements ViewInterface
{

    /**
     * @var string
     */
    private $controller_name;

    /**
     * @var string
     */
    private $action_name;

    public function __construct($controller_name, $action_name)
    {
        $this->controller_name = $controller_name;
        $this->action_name = $action_name;
    }

    public function render($model = null)
    {
        include ('View/'
            . $this->controller_name
            . '/' . $this->action_name
            . '.php');
    }
}