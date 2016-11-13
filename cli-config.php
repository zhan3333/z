<?php
/**
 * Created by PhpStorm.
 * User: 39096
 * Date: 2016/11/13
 * Time: 18:31
 */

// cli-config.php
require_once "bootstrap.php";
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);