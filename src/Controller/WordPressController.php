<?php

namespace Qce\WordPressBundle\Controller;

use Qce\WordPressBundle\WordPress\WordPress;
use Symfony\Component\HttpFoundation\Response;

class WordPressController
{
    public function __construct(
        private WordPress $wp,
    )
    {
    }

    public function __invoke(): Response
    {
        return $this->wp->frontResponse();
    }
}
