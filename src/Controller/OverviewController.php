<?php

namespace App\Controller;

use App\BashProcess\GitStatistics;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    /**
     * @Route("/overview", name="overview")
     */
    public function index()
    {
        $gitStats = new GitStatistics();

        $repositories = $gitStats->listRepositories();

        return $this->render('overview/index.html.twig', [
            'repositories' => $repositories,
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

        return $this->render('overview/repo.html.twig', [
            'contribPerWeekday' => $contribPerWeekday,
            'mostFrequentlyChangedFiles' => $mostFrequentlyChangedFiles,
            'commitsPerHour' => $commitsPerHour,
            'reponame' => $repo,
        ]);
    }
}
