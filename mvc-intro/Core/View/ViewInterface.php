<?php
/**
 * Created by PhpStorm.
 * User: Jivko
 * Date: 3/30/2019
 * Time: 3:30 PM
 */

namespace Core\View;


interface ViewInterface
{
    public function render($model = null);
}