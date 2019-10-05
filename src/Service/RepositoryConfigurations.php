<?php

namespace App\Service;

use Symfony\Component\Config\FileLocator;

class RepositoryConfigurations
{
    public function load($rootDir) : array
    {
        $fileLocator = new FileLocator([$rootDir . '/config/repositories']);
        $configFile = $fileLocator->locate('repo_list.json', null, false);
        $configuredRepositories = json_decode(file_get_contents($configFile[0]), true);

        return $configuredRepositories;
    }
}