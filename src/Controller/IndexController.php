<?php

declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use function substr;

use const DIRECTORY_SEPARATOR;

class IndexController extends AbstractController
{
    public function __construct(private InertiaInterface $inertia)
    {
    }

    #[Route('/')]
    public function index()
    {
        return $this->inertia->render('Homepage', [
            'version' => Kernel::VERSION,
            'projectDir' => $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR,
            'docVersion' => substr(Kernel::VERSION, 0, 3),
        ]);
    }
}
