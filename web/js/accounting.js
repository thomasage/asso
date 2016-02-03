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
    })

}