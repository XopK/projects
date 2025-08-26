let progressValue = 0;
const totalTime = 5000;
const interval = 50;
const steps = totalTime / interval;
const increment = 100 / steps;

function showAlert(response, type) {
    let alertTemplate = $('#alert-template').html();
    let alertContainer = $('.alert-container');

    // вставляем сверху + добавляем mb-2
    alertContainer.prepend(
        $(alertTemplate).addClass('mb-2')
    );

    let currentAlert = alertContainer.find('.block-alert').first();
    let alertElement = currentAlert.find('#alert');
    let alertProgress = currentAlert.find('#alert-progress');

    if (Array.isArray(response)) {
        let message = '<ul class="list-inside space-y-2">';
        response.forEach(function (error) {
            message += `<li class="text-base">${error}</li>`;
        });
        message += '</ul>';
        currentAlert.find('#alert-message').html(message);
    } else {
        currentAlert.find('#alert-message').html(response);
    }

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

    currentAlert.fadeIn();

    currentAlert.on('click', function () {
        $(this).fadeOut(function () {
            $(this).remove();
            progressValue = 0;
        });
    });

    const progressInterval = setInterval(function () {
        progressValue += increment;

        alertProgress.animate({ value: progressValue }, {
            duration: interval,
            easing: 'linear',
            step: function (now) {
                $(this).val(now);
            },
            complete: function () {
                if (progressValue >= 100) {
                    clearInterval(progressInterval);
                    currentAlert.fadeOut(function () {
                        $(this).remove();
                        progressValue = 0;
                    });
                }
            }
        });
    }, interval);
}
