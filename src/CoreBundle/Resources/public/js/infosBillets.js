$(document).ready(function() {

    function age(dateNaissance)
    {
        return new Number((new Date().getTime() - dateNaissance.getTime()) / 31536000000).toFixed(0);
    }

    for(var i=0; i<nbBillets; i++) {
        $('#corebundle_commandeBillets_billets_'+i+'_dateNaissance_month, #corebundle_commandeBillets_billets_'+i+'_dateNaissance_day,' +
            '#corebundle_commandeBillets_billets_'+i+'_dateNaissance_year, #corebundle_commandeBillets_billets_'+i+'_tarifReduit').on('change', function () {
            for(var j=0; j<nbBillets; j++){
                var moisSelect = $('#corebundle_commandeBillets_billets_'+j+'_dateNaissance_month').val();
                var jourSelect = $('#corebundle_commandeBillets_billets_'+j+'_dateNaissance_day').val();
                var anneeSelect = $('#corebundle_commandeBillets_billets_'+j+'_dateNaissance_year').val();
                var dateNaissance = new Date(anneeSelect, moisSelect, jourSelect);
                var ageSelect = age(dateNaissance);

                if($('#corebundle_commandeBillets_billets_'+j+'_tarifReduit').is(':checked') === true){
                    $('#tarif'+j).text('Tarif "Réduit"');
                    $('#prix'+j).text(tarifReduit+' €');
                }
                else if(ageSelect < 4){
                    $('#tarif'+j).text('Tarif "Gratuit"');
                    $('#prix'+j).text('0 €');
                } else if (ageSelect < 12){
                    $('#tarif'+j).text('Tarif "Enfant"');
                    $('#prix'+j).text(tarifEnfant+' €');
                } else if (ageSelect < 60){
                    $('#tarif'+j).text('Tarif "Normal"');
                    $('#prix'+j).text(tarifNormal+' €');
                } else {
                    $('#tarif'+j).text('Tarif "Senior"');
                    $('#prix'+j).text(tarifSenior+' €');
                }
            }
        });
    }
});
