import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';
import toastr from 'toastr';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

window.Swal = Swal;

window.toastr = toastr;
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
};
