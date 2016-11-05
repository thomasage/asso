<?php
namespace AppBundle\Controller\Lesson;

use AppBundle\Entity\Attendance;
use AppBundle\Entity\Lesson;
use AppBundle\Entity\Member;
use AppBundle\Entity\Season;
use AppBundle\Entity\Theme;
use AppBundle\Form\LessonDayCollectionType;
use AppBundle\Form\LessonDeleteType;
use AppBundle\Form\LessonType;
use AppBundle\Form\PlanningCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @param \DateTime $day
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/lesson/add/{day}",
     *        name="app_lesson_add",
     *        methods={"GET","POST"},
     *        requirements={"day"="[0-9]{4}-[0-9]{2}-[0-9]{2}"})
     */
    public function addAction(Request $request, \DateTime $day)
    {
        // Lesson manager
        $lm = $this->get('app.lesson_manager');

        // Edit form
        $lesson = new Lesson();
        $lesson->setDate($day);
        $formEdit = $this->createForm(LessonType::class, $lesson);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $lm->updateLesson($lesson);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('lesson_add.success.added', [], 'lesson')
            );

            // Redirect
            return $this->redirectToRoute('app_lesson_day', ['day' => $lesson->getDate()->format('Y-m-d')]);

        }

        // Render
        return $this->render(
            'lesson/add.html.twig',
            [
                'day' => $day,
                'formEdit' => $formEdit->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param \DateTime $day
     * @return Response
     *
     * @Route("/lesson/day/{day}",
     *        name="app_lesson_day",
     *        methods={"GET","POST"},
     *        requirements={"day"="[0-9]{4}-[0-9]{2}-[0-9]{2}"})
     */
    public function dayAction(Request $request, \DateTime $day)
    {
        // Lessons of the day
        $lm = $this->get('app.lesson_manager');
        $lessons = $lm->findByDate($day);

        // Previous day with lesson
        $previous = $lm->findPreviousDayWithLesson($day);

        // Next day with lesson
        $next = $lm->findNextDayWithLesson($day);

        // Collection form
        $formCollection = $this->createForm(LessonDayCollectionType::class, ['lessons' => $lessons]);
        $formCollection->handleRequest($request);

        if ($formCollection->isSubmitted() && $formCollection->isValid()) {

            // Save data
            $this->getDoctrine()->getManager()->flush();

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('lesson.success.updated', [], 'lesson'));

            // Redirect
            return $this->redirectToRoute('app_lesson_day', ['day' => $day->format('Y-m-d')]);
        }

        // Render
        return $this->render(
            'lesson/day.html.twig',
            [
                'day' => $day,
                'formCollection' => $formCollection->createView(),
                'next' => $next,
                'previous' => $previous,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Lesson $lesson
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/lesson/delete/{lesson}",
     *        name="app_lesson_delete",
     *        methods={"GET","POST"},
     *        requirements={"lesson"="\d+"})
     */
    public function deleteAction(Request $request, Lesson $lesson)
    {
        // Delete form
        $formDelete = $this->createForm(LessonDeleteType::class, $lesson);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Save data
            $day = $lesson->getDate();
            $lm = $this->get('app.lesson_manager');
            $lm->deleteLesson($lesson);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('delete.success.deleted', [], 'lesson')
            );

            // Redirect
            return $this->redirectToRoute('app_lesson_day', ['day' => $day->format('Y-m-d')]);

        }

        // Render
        return $this->render(
            'lesson/delete.html.twig',
            [
                'formDelete' => $formDelete->createView(),
                'lesson' => $lesson,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Lesson $lesson
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/lesson/edit/{lesson}",
     *        name="app_lesson_edit",
     *        methods={"GET","POST"},
     *        requirements={"lesson"="\d+"})
     */
    public function editAction(Request $request, Lesson $lesson)
    {
        // Edit form
        $formEdit = $this->createForm(LessonType::class, $lesson);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $lm = $this->get('app.lesson_manager');
            $lm->updateLesson($lesson);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('edit.success.updated', [], 'lesson')
            );

            // Redirect
            return $this->redirectToRoute('app_lesson_day', ['day' => $lesson->getDate()->format('Y-m-d')]);

        }

        // Render
        return $this->render(
            'lesson/edit.html.twig',
            [
                'formEdit' => $formEdit->createView(),
                'lesson' => $lesson,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/lesson",
     *        name="app_lesson_index",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Season to display
        $season = $this->getUser()->getCurrentSeason();
        if (!$season instanceof Season) {
            $this->addFlash('error', $this->get('translator')->trans(''));

            return $this->redirectToRoute('app_param_season');
        }

        // Calendar of season
        $start = new \DateTime($season->getStart()->format('Y-m-01'));
        $stop = new \DateTime($season->getStop()->format('Y-m-01'));
        $stop->modify('+1 month');
        $months = [];
        foreach (new \DatePeriod($start, new \DateInterval('P1M'), $stop) as $v) {
            $months[] = $v;
        }

        // Days with lessons
        $lessons = [];
        foreach ($em->getRepository('AppBundle:Lesson')->findBySeason($season) as $lesson) {
            $lessons[] = $lesson->getDate()->format('Y-m-d');
        }
        $lessons = new ArrayCollection(array_unique($lessons));

        // Render
        return $this->render(
            'lesson/index.html.twig',
            [
                'lessons' => $lessons,
                'months' => $months,
                'now' => new \DateTime(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/lesson/planning",
     *        name="app_lesson_planning",
     *        methods={"GET","POST"})
     */
    public function planningAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Planning defined
        $elements = $em->getRepository('AppBundle:Planning')->findBy(
            [],
            ['weekday' => 'ASC', 'start' => 'ASC']
        );

        // Edit form
        $formEdit = $this->createForm(PlanningCollectionType::class, ['elements' => $elements]);
        $formEdit->handleRequest($request);

        // Save data
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $data = new ArrayCollection($formEdit->getData()['elements']);
            foreach ($data as $element) {
                $em->persist($element);
            }
            foreach ($elements as $element) {
                if (!$data->contains($element)) {
                    $em->remove($element);
                }
            }
            $em->flush();

            // Redirect
            if ($request->request->has('update_and_build')) {

                // Build lessons
                $lm = $this->get('app.lesson_manager');
                $lm->buildFromPlanning($this->getUser()->getCurrentSeason(), $formEdit->getData()['ignore']);

                // Flash message
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('planning.success.built', [], 'lesson')
                );

                // Redirect
                return $this->redirectToRoute('app_lesson_index');

            } else {

                // Flash message
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('planning.success.updated', [], 'lesson')
                );

                // Redirect
                return $this->redirectToRoute('app_lesson_planning');

            }

        }

        // Render
        return $this->render(
            'lesson/planning.html.twig',
            [
                'formEdit' => $formEdit->createView(),
            ]
        );
    }

    /**
     * @param Lesson $lesson
     * @param Member $member
     * @param int $state
     * @return JsonResponse
     *
     * @Route("/lesson/setAttendance/{lesson}/{member}/{state}",
     *     name="app_lesson_set_attendance",
     *     methods={"POST"},
     *     requirements={"lesson"="\d+","member"="\d+","state"="\d+"},
     *     options={"expose"=true})
     */
    public function setAttendanceAction(Lesson $lesson, Member $member, $state)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Previous attendance
        $attendance = $em->getRepository(Attendance::class)->findByLessonMember($lesson, $member);

        if ($state == 1 || $state == 2) {
            if (!$attendance instanceof Attendance) {
                $attendance = new Attendance();
                $attendance
                    ->setLesson($lesson)
                    ->setMember($member);
            }
            $attendance->setState($state);
            $em->persist($attendance);
            $em->flush();
        } else {
            if ($attendance instanceof Attendance) {
                $em->remove($attendance);
                $em->flush();
            }
        }

        return new JsonResponse('OK');
    }

    /**
     * @param Lesson $lesson
     * @param Theme $theme
     * @param int $state
     * @return JsonResponse
     *
     * @Route("/lesson/setAttendance/{lesson}/{theme}/{state}",
     *     name="app_lesson_set_theme",
     *     methods={"POST"},
     *     requirements={"lesson"="\d+","theme"="\d+","state"="\d+"},
     *     options={"expose"=true})
     */
    public function setThemeAction(Lesson $lesson, Theme $theme, $state)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        if ($state == 1) {
            $lesson->addTheme($theme);
        } else {
            $lesson->removeTheme($theme);
        }
        $em->flush();

        return new JsonResponse('OK');
    }
}
