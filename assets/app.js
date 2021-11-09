// noinspection JSJQueryEfficiency

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import './bootstrap.js';

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

console.log('add close task function');
$('.confirm-close').on('click', function (event) {
    console.log('open close task form');
    event.preventDefault();
    let $dialog = $('#modalConfirmClose');
    // $('.close-message', $dialog).html(event.target.dataset.text);
    $('.closeTaskForm', $dialog).attr('action', event.target.dataset.action);
    $dialog.modal('show');
    $('.btn-success', $dialog).on('click', function () {
        $('.closeTaskForm', $dialog).submit();
    })
});

/**
 *  AdminLTE sidebar state saved in cookie
 */

$.AdminLTESidebarTweak = {};

$.AdminLTESidebarTweak.options = {
    EnableRemember: true,
    NoTransitionAfterReload: false
    //Removes the transition after page reload.
};

$("body").on("collapsed.pushMenu", function(){
    if($.AdminLTESidebarTweak.options.EnableRemember){
        document.cookie = "toggleSidebar=closed";
        console.log('save open sidemenu state');
    }
}).on("expanded.pushMenu", function(){
    if($.AdminLTESidebarTweak.options.EnableRemember){
        document.cookie = "toggleSidebar=opened";
        console.log('save open sidemenu state');
    }
});

if($.AdminLTESidebarTweak.options.EnableRemember){
    var re = new RegExp('toggleSidebar' + "=([^;]+)");
    var value = re.exec(document.cookie);
    var toggleSidebar = (value != null) ? unescape(value[1]) : null;
    console.log('side menu loaded state = ' + toggleSidebar);

    if (toggleSidebar !== 'closed') {
        $("body").removeClass("sidebar-collapse")
    } else {
        if ($.AdminLTESidebarTweak.options.NoTransitionAfterReload) {
            $("body").addClass('sidebar-collapse hold-transition').delay(100).queue(function () {
                $(this).removeClass('hold-transition');
            });
        } else {
            $("body").addClass('sidebar-collapse');
        }
    }
}

