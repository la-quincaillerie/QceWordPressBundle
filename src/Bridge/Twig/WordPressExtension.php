<?php

namespace Qce\WordPressBundle\Bridge\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WordPressExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('action', 'do_action'),
            new TwigFunction('shortcode', 'do_shortcode'),
            new TwigFunction('body_class', 'get_body_class'),
            new TwigFunction('bloginfo', 'bloginfo'),
            new TwigFunction('__', '__'),
            new TwigFunction('translate', 'translate'),
            new TwigFunction('_e', '_e'),
            new TwigFunction('_n', '_n'),
            new TwigFunction('_x', '_x'),
            new TwigFunction('_ex', '_ex'),
            new TwigFunction('_nx', '_nx'),
            new TwigFunction('_n_noop', '_n_noop'),
            new TwigFunction('_nx_noop', '_nx_noop'),
            new TwigFunction('translate_nooped_plural', '_nx_noop'),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('apply', static fn($value, $hook_name, array $args = []) => \apply_filters($hook_name, $value, ...$args), ['is_variadic' => true]),
            new TwigFilter('stripshortcodes', 'strip_shortcodes'),
            new TwigFilter('excerpt', 'wp_trim_words'),
            new TwigFilter('sanitize', 'sanitize_title'),
            new TwigFilter('wpautop', 'wpautop'),
        ];
    }
}
