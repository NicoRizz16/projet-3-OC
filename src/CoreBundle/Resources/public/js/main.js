$(document).ready(function() {

    // Bootstrap datepicker
    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'fr',
        startDate: "today",
        daysOfWeekDisabled: [0,2]
    });

});