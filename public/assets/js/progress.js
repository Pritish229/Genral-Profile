window.updateProgress = function (value) {
    let $progress = $('#progress-bar');

    if ($progress.length) {
        $progress.css('width', value + '%').text(value + '%');
    }
};
