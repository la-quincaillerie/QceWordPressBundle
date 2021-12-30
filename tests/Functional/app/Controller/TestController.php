<?php

namespace TestApp\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/custom', name: 'custom_route')]
    public function __invoke()
    {
        return $this->json('Custom response');
    }
}
