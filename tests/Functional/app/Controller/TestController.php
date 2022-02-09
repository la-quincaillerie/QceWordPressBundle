<?php

namespace TestApp\Controller;

use Qce\WordPressBundle\WordPress\Theme\Attribute\ThemeRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/custom', name: 'custom_route')]
    public function __invoke(): Response
    {
        return $this->json('Custom response');
    }

    #[ThemeRoute('test-file.php')]
    public function index(): Response
    {
        return $this->json('Test');
    }
}
