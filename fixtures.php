<?php

use App\DataFixtures\UserLoader;
use App\Infra\EntityManagerCreator;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require __DIR__.'/vendor/autoload.php';

$loader = new Loader();
$loader->addFixture(new UserLoader());

$executor = new ORMExecutor((new EntityManagerCreator())->getEntityManager(), new ORMPurger());
$executor->execute($loader->getFixtures(), false);
