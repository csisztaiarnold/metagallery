# Meta Gallery

A minimalistic and blazing fast gallery based on htmx, Tailwind, PHP, filesystem and IPTC Photo Metadata. Doesn't require a database.

## Why?

Since I have accumulated a lot of photos in the last few years, I was really in a need for a fast, easily searchable, no-nonsense, straightforward online image gallery without all the bells and whistles. Since I couldn't find any, I have decided to make one on my own, specifically tailored for all my needs.

## Technology

- [PhpRouter](https://github.com/miladrahimi/phprouter) for backend routing.
- [Twig](https://github.com/twigphp/Twig) PHP templating engine.
- [Tailwind](https://tailwindcss.com/) CSS framework.
- [htmx](https://htmx.org/) JavaScript library.

Requirements:

- PHP 8 or above
- PHP Exif library

## How to create a gallery

Rename your `.env.example` file to `.env` and update the configuration file.

Currently, the only option is to place your gallery into the `/public/galleries` folder with a specific naming standard for the folder (otherwise the gallery won't be scanned).

`/public/galleries/YYYY.MM.DD_My_Gallery_Name` (where spaces are replaced by the underscore character)

Place your **JPEG** (no other format is supported yet) images into the gallery. You could name your images whatever you'd like. The thumbs will be created automatically in the `/public/galleries/YYYY.MM.DD_My_Gallery_Name/thumbs` folder.

In case you have a gallery where the date is not important, and it shouldn't appear, use 9999 as the year.

`/public/galleries/9999.11.11_My_Gallery_Where_The_Date_Is_Not_Important/thumbs`

## Technical information

### Routing

All routes are in the `routes.php` file.

### Tailwind

```
npm install -D tailwindcss
npx tailwindcss init
```

After installing Tailwind:

`npx tailwindcss -i ./public/css/src/style.css -o ./public/css/dist/style.css --watch`

### TODO

- Direct link to a single image
- Unlisted galleries
- Image order
- Authentication
- File upload
- Predefined thumbnail size from `.env`
- Lazy loading
