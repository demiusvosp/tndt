/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import './bootstrap.js';
import {createApp} from "vue";
import ActivityTimeline from './components/activity/timeline-widget'
// import TableTaskStatus from './components/tableFilter/task-status'

if (process.env.NODE_ENV === "development") {
    globalThis.__VUE_OPTIONS_API__ = true
    globalThis.__VUE_PROD_DEVTOOLS__ = true;
    globalThis.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__ = true;
} else {
    // different values for production.
    globalThis.__VUE_OPTIONS_API__ = false;
    globalThis.__VUE_PROD_DEVTOOLS__ = false;
    globalThis.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__ = false;
}

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
var activityWidgetPlaceholder = document.getElementById('activity-widget');
if (activityWidgetPlaceholder) {
    const activityWidget = createApp({});
    activityWidget.component('activity-timeline', ActivityTimeline);
    activityWidget.mount('#activity-widget');
}

/* Table filter widget */
var tableFilterWidgetPlaceholder = document.getElementById('table-filter-widget');
if (tableFilterWidgetPlaceholder) {
    const tableFilterWidgetVue = createApp({
        data() {
            return {
                query: "status=closed"
            }
        }
    });
    // tableFilterWidgetVue.component('task-status', TableTaskStatus);
    tableFilterWidgetVue.mount('#table-filter-widget');
}