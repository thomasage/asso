function PlanningAdd() {

    var list = $('#planning_collection_elements');
    var element = $(list.data('prototype').replace(/__name__/g, count));
    element.find('a.element-del').click(function (e) {
        e.preventDefault();
        PlanningDel($(this).parent().parent());
    });
    element.hide();
    list.append(element);
    element.slideDown('fast');
    count++;

}

function PlanningDel(element) {

    element.slideUp('fast', function () {
        $(this).remove();
    });

}