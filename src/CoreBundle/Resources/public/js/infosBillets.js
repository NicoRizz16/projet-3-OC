$(document).ready(function() {

    function age(dateOfBirth)
    {
        return new Number((new Date().getTime() - dateOfBirth.getTime()) / 31536000000).toFixed(0);
    }

    for(var i=0; i<ticketsNb; i++) {
        $('#corebundle_orderTickets_tickets_'+i+'_dateOfBirth_month, #corebundle_orderTickets_tickets_'+i+'_dateOfBirth_day,' +
            '#corebundle_orderTickets_tickets_'+i+'_dateOfBirth_year, #corebundle_orderTickets_tickets_'+i+'_reducedFare').on('change', function () {
            for(var j=0; j<ticketsNb; j++){
                var monthSelect = $('#corebundle_orderTickets_tickets_'+j+'_dateOfBirth_month').val();
                var daySelect = $('#corebundle_orderTickets_tickets_'+j+'_dateOfBirth_day').val();
                var yearSelect = $('#corebundle_orderTickets_tickets_'+j+'_dateOfBirth_year').val();
                var dateOfBirth = new Date(yearSelect, monthSelect, daySelect);
                var ageSelect = age(dateOfBirth);

                if($('#corebundle_orderTickets_tickets_'+j+'_reducedFare').is(':checked') === true){
                    $('#tarif'+j).text('Tarif "Réduit"');
                    $('#prix'+j).text(reduced_fare+' €');
                }
                else if(ageSelect < 4){
                    $('#tarif'+j).text('Tarif "Gratuit"');
                    $('#prix'+j).text('0 €');
                } else if (ageSelect < 12){
                    $('#tarif'+j).text('Tarif "Enfant"');
                    $('#prix'+j).text(child_fare+' €');
                } else if (ageSelect < 60){
                    $('#tarif'+j).text('Tarif "Normal"');
                    $('#prix'+j).text(normal_fare+' €');
                } else {
                    $('#tarif'+j).text('Tarif "Senior"');
                    $('#prix'+j).text(senior_fare+' €');
                }
            }
        });
    }
});
