(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     *
     * The file is enqueued from inc/admin/class-admin.php.
     */

    $(function () {
        $('.need-consent-before-validation').on('click', function (e) {
            e.preventDefault();

            var href = $(e.target).attr('href');

            var text = 'By choosing "Agree" validation request will be sent to the Labs64 NetLicensing ' +
                'to verify valid use of the plugin or theme.<br/>' +
                'Personal data transferred with this request such as Unique Identifiers, Plugin and Theme Details, ' +
                'WordPress Instance Name, Domain Name, System Details of the data subject.<br/><br/>' +
                'For more details on Labs64 NetLicensing data protection provisions visit ' +
                '<a target="_blank" href="https://www.labs64.com/legal/privacy-policy">Privacy Policy</a>' +
                ' and <a target="_blank" href="https://www.labs64.de/confluence/x/vQEKAQ">Privacy Center</a>';

            Swal.fire({
                html: text,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Agree',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = href + '&has_consent=true';
                }
            })
        });

        $('.need-deregister-confirmation').on('click', function (e) {
            e.preventDefault();

            var href = $(e.target).attr('href');

            Swal.fire({
                title:'Deregister plugin/theme',
                html: 'Plugin or theme validation details will be deleted!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Deregister',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = href;
                }
            })
        });

    });
})(jQuery);
