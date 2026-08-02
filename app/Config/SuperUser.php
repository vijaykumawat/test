<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class SuperUser extends BaseConfig
{
    public $username = 'superuser';
    public $password = 'superpass'; // ⚠️ Change to strong secret
}