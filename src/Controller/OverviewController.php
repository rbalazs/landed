<?php

namespace App\Controller;

use App\BashProcess\GitStatistics;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    /**
     * @Route("/overview", name="overview")
     * @Route("/", name="")
     * @Route("/home", name="home")
     */
    public function index()
    {
        $rootDir = $this->getParameter('kernel.project_dir');
        $fileLocator = new FileLocator([$rootDir . '/config/repositories']);
        $configFile = $fileLocator->locate('repo_list.json', null, false);
        $configuredRepositories = json_decode(file_get_contents($configFile[0]), true);

        $gitStats = new GitStatistics();
        $repositories = $gitStats->listRepositories();

        if (!empty($configuredRepositories['repositories'])) {
            array_map(
                function ($repo) use (&$configuredRepositories) {
                    $foundAtKey = array_search($repo, array_column($configuredRepositories['repositories'], 'name'));
                    $configuredRepositories['repositories'][$foundAtKey]['cloned'] = (bool)$foundAtKey;
                },
                $repositories
            );
        }

        return $this->render('overview/index.html.twig', [
            'repositories' => $configuredRepositories['repositories'],
        ]);
    }

    /**
     * @Route("/overview/{repo}", name="overview-per-repo")
     */
    public function perRepo($repo)
    {
        $gitStatistics = new GitStatistics();

        $contribPerWeekday = $gitStatistics->getCommitPerWeekday($repo);

        $mostFrequentlyChangedFiles = $gitStatistics->getMostFrequentlyChangingFiles($repo);

        $commitsPerHour = $gitStatistics->getCommitPerHour($repo);

        $commitsPerAuthors = $gitStatistics->getCommitPerAuthors($repo);

        return $this->render('overview/repo.html.twig', [
            'commitsPerAuthors' => $commitsPerAuthors,
            'contribPerWeekday' => $contribPerWeekday,
            'mostFrequentlyChangedFiles' => $mostFrequentlyChangedFiles,
            'commitsPerHour' => $commitsPerHour,
            'reponame' => $repo,
        ]);
    }

    /**
     * @Route("/clone-repo/{repo}", name="clone-repo")
     */
    public function cloneRepo($repo)
    {
        // TODO: Clone repository.
    }
}
