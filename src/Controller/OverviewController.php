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

        return $this->render('overview/index.html.twig', [
            'repositories' => $repositories,
            'configuredRepositories' => $configuredRepositories
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
}
