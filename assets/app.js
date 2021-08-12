/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// пока не вижу смыла втягивать себе в приложение весь js и scss adminlte с его библиотеками.
//require('../vendor/kevinpapst/adminlte-bundle/Resources/assets/admin-lte');

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// Собственно сам js
console.log('running');

// Modal
$('.need-confirm').on('click', function (event) {
    event.preventDefault();
    console.log(event.currentTarget.dataset.action);
    let $dialog = $('#modalConfirm');
    $('.modal-body', $dialog).html(event.currentTarget.dataset.text);
    $dialog.modal('show');

    $('.btn-success', $dialog).on('click', {'action': event.currentTarget.dataset.action}, function () {
        document.location = event.currentTarget.dataset.action;
    })
});
