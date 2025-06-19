/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import './styles/app.css';
import $ from 'jquery';
import Swal from 'sweetalert2';
global.Swal = Swal;
global.$ = global.jQuery = $;
import PerfectScrollbar from 'perfect-scrollbar';

import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

import 'datatables.net-responsive-bs5';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

// Expose jQuery globally if needed

// js files
import 'select2/dist/css/select2.min.css';
import 'select2';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';
import './libs/perfect-scrollbar/perfect-scrollbar'

$('.select2-dropdown-multiple').select2({
    theme: "bootstrap-5",
    multiple: true,
    closeOnSelect: false,
    width: '100%'

});
$('.select2-dropdown-single').select2({
    theme: "bootstrap-5",
    width: '100%'
});

var $container = $('.scrollable-container');
if ($container.length) {
    $container.each(function () {
        new PerfectScrollbar(this);
        $(this).removeClass('overflow-hidden'); // enable scrolling
    });
}
