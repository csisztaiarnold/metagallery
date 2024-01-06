Array.prototype.forEach.call(document.getElementsByClassName('thumbnail-image'), function (thumbnail_image) {
    thumbnail_image.addEventListener('click', (e) => {
        // Create a new (hidden) element and add it to the container.
        const image_source = thumbnail_image.getAttribute('data-fullsize');
        const image_caption = thumbnail_image.getAttribute('data-caption');

        const container = document.getElementById('fullscreen-image-container');
        const click_to_close = document.getElementById('click-to-close');
        container.classList.remove('w-0', 'h-0');
        container.classList.add('w-screen', 'h-screen');
        container.style.backgroundImage = 'url(' + image_source + ')';
        click_to_close.classList.remove('hidden');

        // On close.
        container.addEventListener('click', (e) => {
            container.classList.remove('w-screen', 'h-screen');
            container.classList.add('w-0', 'h-0');
            click_to_close.classList.add('hidden');
        });
    });
});

Array.from(document.getElementsByClassName('download-icon')).forEach(function(download_icon) {
    download_icon.addEventListener("click", function (ev) {
        ev.stopPropagation();
        return false;
    }, false);
});

