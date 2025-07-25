let progressValue = 0;
const totalTime = 8000;
const interval = 50;
const steps = totalTime / interval;
const increment = 100 / steps;

function showAlert(response, type) {
    let alertTemplate = $('#alert-template').html();
    let alertContainer = $('.alert-container');
    alertContainer.html(alertTemplate);

    if (Array.isArray(response)) {
        let message = '<ul class="list-inside space-y-2">';
        response.forEach(function (error) {
            message += `<li class="text-base">${error}</li>`;
        });
        message += '</ul>';

        $('#alert-message').html(message);
    } else {
        $('#alert-message').html(response);
    }

    let alertElement = $('#alert');
    let alertProgress = $('#alert-progress');
    switch (type) {
        case 'error':
            alertElement.addClass('alert-error');
            alertProgress.addClass('progress-error');
            break;
        case 'warning':
            alertElement.addClass('alert-warning');
            alertProgress.addClass('progress-warning');
            break;
        case 'success':
            alertElement.addClass('alert-success');
            alertProgress.addClass('progress-success');
            break;
        default:
            alertElement.addClass('alert-info');
            alertProgress.addClass('progress-info');
            break;
    }

    $('.block-alert').fadeIn();

    $('.block-alert').on('click', function () {
        $(this).fadeOut(function () {
            $(this).remove();
            progressValue = 0;
        });
    });


    const progressInterval = setInterval(function () {
        progressValue += increment;

        $('#alert-progress').animate({value: progressValue}, {
            duration: interval, easing: 'linear', step: function (now) {
                $(this).val(now);
            }, complete: function () {
                if (progressValue >= 100) {
                    clearInterval(progressInterval);
                    $('.block-alert').fadeOut(function () {
                        $(this).remove();
                        progressValue = 0;
                    });
                }
            }
        });
    }, interval);
}
