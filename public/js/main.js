Array.prototype.forEach.call(document.getElementsByClassName('thumbnail-image'), function (thumbnail_image) {
    thumbnail_image.addEventListener('click', (e) => {

        // Now create a new (hidden) one and add it to the container.
        const image_source = thumbnail_image.getAttribute('data-fullsize');
        const image_caption = thumbnail_image.getAttribute('data-caption');

        const container = document.getElementById('fullscreen-image-container');
        container.classList.remove('w-0', 'h-0');
        container.classList.add('w-screen', 'h-screen', 'p-5');
        container.style.backgroundImage = 'url(' + image_source + ')';

        // On close.
        container.addEventListener('click', (e) => {
            container.classList.remove('w-screen', 'h-screen', 'p-5');
            container.classList.add('w-0', 'h-0');
        });

    });
});

Array.from(document.getElementsByClassName('download-icon')).forEach(function(download_icon) {

    download_icon.addEventListener("click", function (ev) {
        console.log('popdopop')
        ev.stopPropagation();
        return false;
    }, false);

});

