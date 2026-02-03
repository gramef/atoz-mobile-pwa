import Vue from 'vue'
import 'select2'
import './bootstrap'
import flatpickr from "flatpickr/dist/flatpickr"

Vue.config.productionTip = false

Vue.component('translator-job-dropzone', require('./components/TranslatorJobDropzone.vue').default)
Vue.component('document-dropzone', require('./components/DocumentDropzone.vue').default)
Vue.component('translation-file-dropzone', require('./components/TranslationFileDropzone.vue').default)
Vue.component('quote-form', require('./components/QuoteForm.vue').default)

const app = new Vue({
    el: '#app',
})

$('#hamburger').on('click', function () {
    $(this).toggleClass('is-active')
    $('#headerCollapse').toggleClass('header__collapse--open')
});

$('#headerLogout').on('click', function () {
    $(this).toggleClass('header__logout--open')
});

$('#filterForm .section__input').not('#dateFilter').on('change', () => {
    $('#filterForm').submit()
});

$('.job__status-dropdown').on('change', function () {
    const msg = confirm('Are you sure you want to update this job\'s status?')

    if (msg) {
        $(this).parent('form').submit()
    } else {
        $(this).val($(this).data('status'))
    }
});

$('.input--select').each(function () {
    $(this)
        .toggleClass('input--not-chosen', !$(this).val())
        .change(() => $(this).toggleClass('input--not-chosen', !$(this).val()))
});

$('#radioButtons input[type="radio"]').on('click', function () {
    window.location = $(this).data('link')
})

$('.message__close').on('click', function () {
    $(this).parent().addClass('message--close')
});

$('#languagesSelect').select2({
    tags: true,
    placeholder: "Select a language",
    width: 'resolve'
});

$('[data-button="reject"').click(function() {
    const msg = confirm('Are you sure you want to reject this request?')
    if (!msg) return

    $('#rejectForm').submit()
});

$('[data-button="cancel"]').click(function() {
    if ($(this).data('is-within-24-hours')) {
        $('#24HourModal').modal('show', {
            route: $(this).data('route'),
        })
    } else {
        $('#cancelModal').modal('show', {
            route: $(this).data('route'),
        })
    }
});

$('#unmatchedAgentsSelect').select2()

$('#unmatchedAgentsSelect').change(function() {
    $(this).parents('form').submit()
});
