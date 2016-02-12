function CategoryAdd() {

    var list = $('.categories');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('a.category-del').click(function (e) {
        e.preventDefault();
        CategoryDel($(this).parent());
    });
    element.hide();
    list.append(element);
    element.slideDown('fast');
    count++;

}

function CategoryDel(element) {

    element.slideUp('fast', function () {
        $(this).remove();
    });

}

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

function TransactionCopyAdd() {

    var list = $('#transaction_copies');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('a.copy-del').click(function (e) {
        e.preventDefault();
        TransactionCopyDel($(this).parent().parent());
    });
    list.append(element);
    count++;

}

function TransactionCopyDel(element) {

    element.slideUp('fast', function () {
        $(this).remove();
    });

}