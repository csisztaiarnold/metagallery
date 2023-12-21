<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\FileController as FileController;
use Core\Controller;
use Core\View\View;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * The MainController class.
 *
 * @package App\Controllers
 */
class MainController extends Controller
{
    /**
     * @var \App\Controllers\FileController
     */
    public \App\Controllers\FileController $file;

    /**
     * The MainController constructor.
     */
    public function __construct(FileController $file)
    {
        $this->file = $file;
    }

    /**
     * The index page.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): void
    {
        View::make('index.twig', [
            'rnd' => rand(0, 9999999),
            'galleries' => $this->file->readGalleries(),
            'index' => true,
        ]);
    }

    public function searchImage() {

        $galleries = $this->file->readGalleries();
        $found_image_array = [];
        $index = 0;
        foreach($this->file->readGalleries() as $gallery_item) {
            foreach($gallery_item['images'] as $image) {
                if (str_contains(strtolower($image['comment'] ? $image['comment'] : ''), strtolower($_POST['search'])) || str_contains(strtolower($gallery_item['folder']), strtolower($_POST['search'])) ) {
                    $found_image_array[$index] = $image;
                    $found_image_array[$index]['gallery'] = $gallery_item['folder'];
                    $index++;
                }
            }
        }

        if(empty($_POST['search'])) {
            View::make('index.twig', [
                'rnd' => rand(0, 9999999),
                'galleries' => $this->file->readGalleries(),
                'standalone' => true,
                'index' => true,
            ]);
        } else {
            View::make('gallery.twig', [
                'rnd' => rand(0, 9999999),
                'galleries' => $this->file->readGalleries(),
                'folder' => '',
                'date' => '',
                'gallery_title' => 'Search',
                'images' => $found_image_array,
                'standalone' => true,
                'search' => true,
                'number_of_images_found' => count($found_image_array),
            ]);
        }
    }

    /**
     * The gallery page.
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function imageList(): void
    {
        if (!empty($_POST)) {
            $gallery = $_POST['gallery'];
            $standalone = true;
        } else {
            $url_array = explode('/', $_SERVER['REDIRECT_URL']);
            $gallery = '';
            if (!isset($url_array[2])) {
                header('Location: /');
            } else {
                $gallery = $url_array[2];
            }
            $standalone = false;
        }

        if (empty($gallery)) {
            View::make('index.twig', [
                'rnd' => rand(0, 9999999),
                'galleries' => $this->file->readGalleries(),
                'standalone' => true,
                'index' => true,
            ]);
        } else {
            View::make('gallery.twig', [
                'rnd' => rand(0, 9999999),
                'galleries' => $this->file->readGalleries(),
                'folder' => $gallery,
                'date' => $this->file->readableDate($gallery),
                'gallery_title' => $this->file->readableGalleryName($gallery),
                'images' => $this->file->readImagesFromGallery($gallery, 'desc'),
                'standalone' => $standalone,
            ]);
        }
    }
}