<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\ImageController;
use App\Services\IPTC;
use Core\Controller;
use Exception;

/**
 * The FileController class.
 *
 * @package App\Controllers
 */
class FileController extends Controller
{
    /**
     * @var string
     */
    public string $path_to_galleries = '/../../public/galleries';

    /**
     * @var ImageController
     */
    public ImageController $image;

    /**
     * The MainController constructor.
     */
    public function __construct(ImageController $image)
    {
        $this->image = $image;
    }

    /**
     * Reads all the subfolders in the gallery folder and returns them in an array.
     *
     * @return array
     * @throws Exception
     */
    public function readGalleries(): array
    {
        $folder_array = [];
        $folders = preg_grep('/^([^.])/', scandir(__DIR__ . $this->path_to_galleries, SCANDIR_SORT_DESCENDING));
        foreach ($folders as $folder) {
            if ($folder !== 'thumbs') {
                // Is the date in correct format?
                if (preg_match("/^[0-9]{4}.(0[1-9]|1[0-2]).(0[1-9]|[1-2][0-9]|3[0-1])$/", explode('_', $folder)[0])) {
                    $gallery_title = $this->readableGalleryName($folder);
                    $date = $this->readableDate($folder);
                    // Is the year 9999? Then we won't store the date info.
                    if(explode('.', explode('_', $folder)[0])[0] === '9999') {
                        $date = '';
                    }
                    $folder_array[] = [
                        'folder' => $folder,
                        'title' => $gallery_title,
                        'date' => $date,
                        'images' => $this->readImagesFromGallery($folder, 'asc'),
                    ];
                }
            }
        }
        return $folder_array;
    }

    /**
     * Reads all the image files in a defined folder and returns them in an array.
     *
     * @param string $folder
     * @param string $order
     * @return array
     * @throws Exception
     */
    public function readImagesFromGallery(string $folder, string $order): array
    {
        $gallery_path = __DIR__ . $this->path_to_galleries;
        if (is_dir($gallery_path . '/' . $folder)) {
            $image_array = [];
            $images = preg_grep(
                '/^([^.])/',
                scandir(
                    $gallery_path . '/' . $folder,
                    $order === 'desc' ? SCANDIR_SORT_DESCENDING : SCANDIR_SORT_ASCENDING
                )
            );
            foreach ($images as $image) {
                if ($image !== 'thumbs') {
                    $image_path = $gallery_path . '/' . $folder . '/' . $image;
                    if(exif_imagetype($image_path) === IMAGETYPE_JPEG) {
                        [$width, $height] = getimagesize($image_path);
                        $iptc = new Iptc($gallery_path . '/' . $folder . '/' . $image);
                        $caption = $iptc->fetch(Iptc::CAPTION);
                        $this->image->createThumbnailIfDoesntExists($folder, $image, 700, 1000);
                        $image_array[] = [
                            'filename' => $image,
                            'gallery_name' => $this->readableGalleryName($folder),
                            'width' => $width,
                            'height' => $height,
                            'aspect' => $width > $height ? 'video' : 'square',
                            'comment' => $caption ?? '',
                        ];
                    }
                }
            }
            return $image_array;
        } else {
            return [];
        }
    }

    /**
     * @param string $folder
     * @return string
     */
    public function readableGalleryName(string $folder): string
    {
        $exploded_folder = explode('_', $folder);
        return trim(str_replace('_', ' ', str_replace($exploded_folder[0], '', $folder)));
    }

    /**
     * @param string $folder
     * @return string
     */
    public function readableDate(string $folder): string
    {
        $exploded_folder = explode('_', $folder);
        if(explode('.', $exploded_folder[0])[0] === '9999') {
            return '';
        }
        return trim(str_replace('.', '. ', $exploded_folder[0])) . '.';
    }
}