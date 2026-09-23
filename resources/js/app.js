import './bootstrap';

// jQuery
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

jQuery.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content'),
    },
});

// DataTables (binding ke jQuery)
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

DataTable.use(jQuery);
window.DataTable = DataTable;

DataTable.defaults.language = {
    search: 'Cari:',
    searchPlaceholder: 'Ketik untuk mencari...',
    lengthMenu: 'Tampilkan _MENU_ data',
    info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
    infoEmpty: 'Tidak ada data',
    infoFiltered: '(disaring dari _MAX_ total data)',
    zeroRecords: 'Tidak ada data yang cocok',
    emptyTable: 'Tidak ada data',
    loadingRecords: 'Memuat...',
    processing: 'Memuat...',
    paginate: {
        first: '«',
        previous: '‹',
        next: '›',
        last: '»',
    },
};

// Select2
import select2 from 'select2';
select2(jQuery);
import 'select2/dist/css/select2.min.css';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Toastr
import toastr from 'toastr';
window.toastr = toastr;
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
};

// Alpine.js
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

/**
 * Semua <select> otomatis jadi Select2. Kotak pencarian hanya muncul kalau
 * opsinya >= 10 (minimumResultsForSearch) -- select pendek (bulan, status,
 * jenis surat) tetap polos, select panjang (unit pengirim, direktur, dst)
 * otomatis searchable. Opt-out per elemen: class "no-select2".
 */
window.initSelect2 = function (root = document) {
    jQuery(root).find('select:not(.no-select2)').each(function () {
        const $el = jQuery(this);

        if ($el.hasClass('select2-hidden-accessible')) {
            return;
        }

        $el.select2({
            width: '100%',
            minimumResultsForSearch: 15,
            language: {
                noResults: () => 'Tidak ada hasil',
                searching: () => 'Mencari...',
            },
        });
    });
};

// Flatpickr Datepicker
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

flatpickr.localize(Indonesian);
window.flatpickr = flatpickr;

window.initDatepicker = function (root = document) {
    jQuery(root).find('.datepicker-input:not(.flatpickr-input)').each(function () {
        const el = this;
        const $el = jQuery(el);
        const mode = $el.data('mode') || 'single';
        const dateFormat = $el.data('date-format') || 'Y-m-d';
        const altFormat = $el.data('alt-format') || (mode === 'range' ? 'd M Y' : 'd F Y');

        const fp = flatpickr(el, {
            mode: mode,
            dateFormat: dateFormat,
            altInput: true,
            altFormat: altFormat,
            altInputClass: $el.attr('class') + ' flatpickr-alt-input',
            allowInput: false,
            locale: Indonesian,
            onChange: function (selectedDates, dateStr, instance) {
                if (mode === 'range' && selectedDates.length < 2) {
                    return;
                }
                instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                if ($el.hasClass('filter-submit') || $el.attr('data-auto-submit') === 'true') {
                    if (instance.input.form) {
                        instance.input.form.submit();
                    }
                }
            }
        });

        // Event listener tombol (x) clear pada datepicker
        $el.closest('.relative').find('.datepicker-clear').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fp.clear();
            el.dispatchEvent(new Event('change', { bubbles: true }));
            if ($el.hasClass('filter-submit') || $el.attr('data-auto-submit') === 'true') {
                if (el.form) {
                    el.form.submit();
                }
            }
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initSelect2();
    window.initDatepicker();
});

// Select2 hanya men-trigger `change` versi jQuery. Dispatch event `change`
// native agar x-model Alpine dan atribut onchange="..." pada form filter
// ter-trigger otomatis saat user memilih opsi lewat dropdown Select2.
jQuery(document).on('select2:select select2:unselect select2:clear', 'select', function () {
    this.dispatchEvent(new Event('change', { bubbles: true }));
});

// Dropdown "Tampilkan N data" bawaan DataTables juga jadi Select2 (tanpa
// kotak pencarian -- opsinya cuma beberapa angka).
jQuery(document).on('init.dt', (e, settings) => {
    jQuery(settings.nTableWrapper).find('.dt-length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto',
    });
});
