<?php
/**
 * Created by PhpStorm.
 * User: Jivko
 * Date: 3/30/2019
 * Time: 6:16 PM
 */

namespace Core\App;


use Core\View\ViewInterface;

class Application
{
    /**
     * @var string
     */
    private $controller_name;

    /**
     * @var string
     */
    private $action_name;

    /**
     * @var array
     */
    private $params;

    public function __construct(string $controller_name, string $action_name, array $params)
    {
        $this->controller_name = $controller_name;
        $this->action_name = $action_name;
        $this->params = $params;
    }

    public function run(ViewInterface $view){
        $controller_name = 'Controller\\' . $this->controller_name .'Controller';
        $controller = new $controller_name($view);

        //call_user_func_array(
        //    [$this->controller_name, $this->action_name],
        //    $this->params
        //);

        // $controller->$action_name($params[0], $params[1]);
        // $controller->$this->action_name($this->params);
    }
}