<?php

namespace Qce\WordPressBundle\Bridge\Twig;

class WordPressVariable
{
    public function getLanguageAttributes(string $doctype = 'html'): string
    {
        return \get_language_attributes($doctype);
    }

    public function getTitle(): string
    {
        return \get_the_title();
    }

    public function getBloginfo(string $show = '', string $filter = 'display'): string
    {
        return \get_bloginfo($show, $filter);
    }

    /**
     * @param string|string[] $class
     */
    public function getBodyClass(string|array $class = ''): string
    {
        return \implode(' ', \get_body_class($class));
    }
}
