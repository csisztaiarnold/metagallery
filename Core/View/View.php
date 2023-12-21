<?php
declare(strict_types=1);

namespace Core\View;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class View
{
    /**
     * The Twig\Environment singleton.
     *
     * @var Environment
     */
    public static $instance;

    private function __construct()
    {
    }

    public static function twigEnvironment(): Environment
    {
        if (is_null(static::$instance)) {
            static::$instance = static::buildTwigEnvironment();
        }

        return static::$instance;
    }

    public static function buildTwigEnvironment(): Environment
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../public/templates');
        $twig = new Environment($loader, [
            // TODO: turn on or off cache based on environment (dev/prod)
            // 'cache' => __DIR__ . '/cache/compilation_cache',
        ]);

        $settings = parse_ini_file(__DIR__ . '/../../.env');

        $twig->addGlobal('main_logo', $settings['MAIN_LOGO']);
        $twig->addGlobal('main_description', $settings['MAIN_DESCRIPTION']);
        return $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public static function make($view, array $args = []): void
    {
        echo static::twigEnvironment()->render($view, $args);
    }
}