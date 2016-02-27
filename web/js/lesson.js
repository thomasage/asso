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

function LessonAttendance(lesson, member, active) {

    $.ajax(
        {
            url: Routing.generate('app_lesson_set_attendance', {
                lesson: lesson,
                member: member,
                active: active ? 0 : 1
            }),
            method: 'POST',
            success: function (data) {
                if (data.status == 1) {
                    $('[data-lesson=' + lesson + '] [data-member=' + member + ']').addClass('active');
                } else {
                    $('[data-lesson=' + lesson + '] [data-member=' + member + ']').removeClass('active');
                }
            }
        }
    );

}