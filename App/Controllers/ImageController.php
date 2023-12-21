<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\IPTC;
use Core\Controller;

/**
 * The MainController class.
 *
 * @package App\Controllers
 */
class ImageController extends Controller
{
    /**
     * @var string
     */
    public string $path_to_galleries = '/../../public/galleries';

    /**
     * Creates a thumbnail file if it doesn't exist already.
     *
     * @param string $folder
     * @param string $filename
     * @param int    $maxwidth
     * @param int    $maxheight
     * @return void
     */
    public function createThumbnailIfDoesntExists(string $folder, string $filename, int $maxwidth, int $maxheight): void
    {
        $thumbs_folder = __DIR__ . $this->path_to_galleries . '/' . $folder . '/thumbs';
        if (!file_exists($thumbs_folder . '/' . $filename)) {
            if (!is_dir($thumbs_folder)) {
                mkdir($thumbs_folder, 0755, true);
            }
            $source_image = imagecreatefromjpeg(__DIR__ . $this->path_to_galleries . '/' . $folder . '/' . $filename);
            $width = imagesx($source_image);
            $height = imagesy($source_image);

            if ($height > $width) {
                $ratio = $maxheight / $height;
                $newheight = $maxheight;
                $newwidth = floor($width * $ratio);
            } else {
                $ratio = $maxwidth / $width;
                $newwidth = $maxwidth;
                $newheight = floor($height * $ratio);
            }
            $tmp_image = imagecreatetruecolor((int)$newwidth, (int)$newheight);
            imagecopyresampled($tmp_image, $source_image, 0, 0, 0, 0, (int)$newwidth, (int)$newheight, $width, $height);
            imagejpeg($tmp_image, $thumbs_folder . '/' . $filename);
        }
    }

}