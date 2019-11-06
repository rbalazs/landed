<?php

namespace App\Controller;

use App\BashProcess\GitCommands;
use App\BashProcess\GitStatistics;
use App\Service\RepositoryConfigurations;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    /**
     * @var RepositoryConfigurations
     */
    private $repositoryConfigurations;

    /**
     * @var GitStatistics
     */
    private $gitStatistics;

    /**
     * @var GitCommands
     */
    private $gitCommands;

    /**
     * @param RepositoryConfigurations $repositoryConfigurations
     * @param GitStatistics $gitStatistics
     * @param GitCommands $gitCommands
     */
    public function __construct(
        RepositoryConfigurations $repositoryConfigurations,
        GitStatistics $gitStatistics,
        GitCommands $gitCommands
    )
    {
        $this->repositoryConfigurations = $repositoryConfigurations;
        $this->gitStatistics = $gitStatistics;
        $this->gitCommands = $gitCommands;
    }

    /**
     * @Route("/overview", name="overview")
     * @Route("/", name="")
     * @Route("/home", name="home")
     */
    public function index()
    {
        $configuredRepositories = $this->repositoryConfigurations->load($this->getParameter('kernel.project_dir'));

        $repositories = $this->gitStatistics->listRepositories();

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
     * @param $repo
     * @return Response
     */
    public function perRepo($repo)
    {
        $contribPerWeekday = $this->gitStatistics->getCommitPerWeekday($repo);
        $mostFrequentlyChangedFiles = $this->gitStatistics->getMostFrequentlyChangingFiles($repo);
        $commitsPerHour = $this->gitStatistics->getCommitPerHour($repo);
        $commitsPerAuthors = $this->gitStatistics->getCommitPerAuthors($repo);
        $masterMergesPerAuthor = $this->gitStatistics->getMasterMergesPerAuthors($repo);

        $htmlSource = $this->renderView('overview/repo.html.twig', [
            'commitsPerAuthors' => $commitsPerAuthors,
            'contribPerWeekday' => $contribPerWeekday,
            'mostFrequentlyChangedFiles' => $mostFrequentlyChangedFiles,
            'commitsPerHour' => $commitsPerHour,
            'masterMergesPerAuthor' => $masterMergesPerAuthor,
            'reponame' => $repo,
        ]);


        try {
            $fsObject = new Filesystem();
            $projectDir = $this->getParameter('kernel.project_dir');
            $filePath = $projectDir . "/public/ls/" . $repo . ".html";

            if (!$fsObject->exists($filePath))
            {
                $fsObject->touch($filePath);
                $fsObject->chmod($filePath, 0777);
                $fsObject->dumpFile($filePath, $htmlSource);
            }
        } catch (IOExceptionInterface $exception) {
            echo "Error creating file at ". $exception->getPath() . PHP_EOL . $exception->getMessage();
        }

        return new Response($htmlSource);
    }

    /**
     * @Route("/clone-repo/{repo}", name="clone-repo")
     * @param $repo
     * @return RedirectResponse
     * @throws Exception
     */
    public function cloneRepo($repo)
    {
        $configuredRepositories = $this->repositoryConfigurations->load($this->getParameter('kernel.project_dir'));

        if (empty($configuredRepositories['repositories'])) {
            throw new Exception('Missing configuration files.');
        }

        $foundAtKey = array_search($repo, array_column($configuredRepositories['repositories'], 'name'));
        $url = $configuredRepositories['repositories'][$foundAtKey]['url'];

        try {
            $this->gitCommands->cloneRepository($url);
        } catch (Exception $exception) {
            $this->addFlash('process-error', 'Error:' . $exception->getMessage());
        }

        return $this->redirectToRoute('overview');
    }
}
