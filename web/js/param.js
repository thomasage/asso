function ParamSeasonActive(season) {

    $.ajax(
        {
            url: Routing.generate('app_param_season_active', {season: season}),
            method: 'GET'
        }
    );

}

function ParamRankSort() {

    var ranks = [];
    $('.ranks > tr[data-reference]').each(function () {
        ranks.push($(this).data('reference'));
    });

    $.ajax(
        {
            url: Routing.generate('app_param_rank_sort', {ranks: ranks.join(',')}),
            method: 'PUT',
            success: function (data) {
                for (var i = 0; i < ranks.length; i++) {
                    $('.ranks > tr[data-reference=' + ranks[i] + '] > td:first-of-type').html('#' + (i + 1));
                }
            }
        }
    )

}