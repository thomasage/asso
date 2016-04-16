<?php
namespace AppBundle\Controller\Lesson;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingAttendancesAction()
    {
        // Doctrine
        $em = $this->getDoctrine()->getRepository('AppBundle:Lesson');

        // Missing attendances
        $lessons = $em->findMissingAttendances($this->getUser()->getCurrentSeason());

        // Render
        return $this->render(
            'lesson/widget/missing.attendances.html.twig',
            [
                'lessons' => $lessons,
            ]
        );
    }
}
