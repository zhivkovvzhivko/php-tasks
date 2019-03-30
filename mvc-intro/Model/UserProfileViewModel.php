<?php
/**
 * Created by PhpStorm.
 * User: Jivko
 * Date: 3/30/2019
 * Time: 5:41 PM
 */

namespace Model;

class UserProfileViewModel
{
    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

    /**
     * UserProfileViewModel constructor.
     */
    public function __construct(string $first_name, string $last_name)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

}