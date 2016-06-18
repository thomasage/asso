function TransactionAdd() {

    var list = $('#transaction_details');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('a.transaction-del').click(function (e) {
        e.preventDefault();
        TransactionDel($(this).parent().parent());
    });
    list.append(element);
    count++;

}

function TransactionDel(element) {

    element.slideUp('fast', function () {
        $(this).remove();
    });

}
