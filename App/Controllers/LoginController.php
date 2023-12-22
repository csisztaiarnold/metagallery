<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\View\View;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * The LoginController class.
 *
 * @package App\Controllers
 */
class LoginController extends Controller
{
    /**
     * The login page.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function signIn(): void
    {
        View::make('login.twig', [
        ]);
    }

}