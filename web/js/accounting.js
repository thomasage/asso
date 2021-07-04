function TransactionAdd(urlAutocomplete) {

    var list = $('#transaction_details');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('input[id^=transaction_details_][id$=_category]').autocomplete({
        source: urlAutocomplete
    });
    element.find('a.transaction-del').click(function (e) {
        e.preventDefault();
        TransactionDel($(this).parent().parent());
    });
    list.append(element);
    element.find('input').eq(0).focus();
    count++;

}

function TransactionDel(element) {

    element.slideUp('fast', function () {
        $(this).remove();
    });

}

function ForecastBudgetItemAdd() {

    var list = $('#forecast_budget_item_collection_items');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('input[id$=_category]').autocomplete({
        source: Routing.generate('app_accounting_autocomplete_category')
    });
    element.find('a.item_delete').click(function (e) {
        e.preventDefault();
        ForecastBudgetItemDelete($(this).parent().parent());
    });
    list.append(element);
    element.find('input').eq(0).focus();
    count++;

}

function ForecastBudgetItemDelete(element) {

    element.remove();

}
