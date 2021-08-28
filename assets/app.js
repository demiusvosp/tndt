/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import './bootstrap.js';

// Собственно сам js
console.log('running');

/**
 * Autoupdate - small form will submited on change any field
 */
$('form.autoupdate').on('change', 'button,input', function (event) {
    console.log('submit autoupdate form');
    event.delegateTarget.submit();
});

// Modal
$('.need-confirm').on('click', function (event) {
    event.preventDefault();
    let $dialog = $('#modalConfirm');
    $('.modal-body', $dialog).html(event.target.dataset.text);
    $dialog.on('hide.bs.modal', function () {
        $('modal_body', this).html('');
    });
    $dialog.modal('show');

    $('.btn-success', $dialog).on('click', {'action': event.target.dataset.action}, function () {
        document.location = event.currentTarget.dataset.action;
    })
});
