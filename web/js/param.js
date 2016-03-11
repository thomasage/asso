function ParamSeasonActive(season) {

    console.log(season);

    $.ajax(
        {
            url: Routing.generate('app_param_season_active', {season: season}),
            method: 'POST'
        }
    );

}