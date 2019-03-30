<?php
/**
 * Created by PhpStorm.
 * User: Jivko
 * Date: 3/30/2019
 * Time: 1:50 PM
 */

namespace Controller;

use Core\View\View;
use Core\View\ViewInterface;
use Model\UserProfileViewModel;

class UsersController
{
    /**
     * @var View
     */
    private $view;

    /**
     * UsersController constructor.
     * @param ViewInterface $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function profile(string $first_name, string $last_name){
        $model = new UserProfileViewModel($first_name, $last_name);
        $this->view->render($model);
    }

    public function register(){
//        echo ' first: '. $first_name . ' last: ' . $last_name;
        $this->view->render();
    }
}