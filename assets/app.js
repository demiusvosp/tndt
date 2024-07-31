/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import './bootstrap.js';
import Vue from "vue";
import ActivityTimeline from './components/activity/timeline-widget'

// Собственно сам js
console.log('running');

// $(document).on('expanded.pushMenu', function(event) { console.log('sidebar expanded'); });
// $(document).on('collapsed.pushMenu', function(event) { console.log('sidebar collapsed'); });

/**
 * Autoupdate - small form will submitted on change any field
 */
$('form.autoupdate').on('change', 'button,input', function (event) {
    console.log('submit autoupdate form');
    event.delegateTarget.submit();
});

/**
 * Modal - universal modal dialog for confirm danger action
 */
$('.need-confirm').on('click', function (event) {
console.log('need confirm');
    event.preventDefault();
    let $dialog = $('#modalConfirm');
    let text = event.target.dataset.text.replace(/(?:\\[rn]|[\r\n])/g, "<br/>");
    $('.modal-body', $dialog).html(text);
    $('form', $dialog).attr('action', event.target.dataset.action);

    $dialog.on('hide.bs.modal', function () {
        $('modal_body', this).html('');
    });
    $dialog.modal('show');
});


$('.confirm-close').on('click', function (event) {
    event.preventDefault();
    let $dialog = $('#modalConfirmClose');
    $('.closeTaskForm', $dialog).attr('action', event.target.dataset.action);
    $dialog.modal('show');
    $('.btn-success', $dialog).on('click', function () {
        $('.closeTaskForm', $dialog).submit();
    })
});


/* Activity widget */
var activityWidgetVue = new Vue({
    components: {
        ActivityTimeline
    }
});
var activityWidgetPlaceholder = document.getElementById('activity-widget');
if (activityWidgetPlaceholder) {
    activityWidgetVue.$mount('#activity-widget');
}