<?php
namespace AppBundle\Controller\Lesson;

use AppBundle\Entity\Planning;
use AppBundle\Form\PlanningCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
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
        // Render
        return $this->render(
            'lesson/day.html.twig',
            array(
                'day' => $day,
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
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

        // Calendar of season
        $start = new \DateTime($season->getStart()->format('Y-m-01'));
        $stop = new \DateTime($season->getStop()->format('Y-m-01'));
        $stop->modify('+1 month');
        $months = array();
        foreach (new \DatePeriod($start, new \DateInterval('P1M'), $stop) as $v) {
            $months[] = $v;
        }

        // Days with lessons
        $lessons = array();
        foreach ($em->getRepository('AppBundle:Lesson')->findBySeason($season) as $lesson) {
            $lessons[] = $lesson->getDate()->format('Y-m-d');
        }
        $lessons = new ArrayCollection(array_unique($lessons));

        // Render
        return $this->render(
            'lesson/index.html.twig',
            array(
                'lessons' => $lessons,
                'months' => $months,
                'now' => new \DateTime(),
            )
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
            array(),
            array('weekday' => 'ASC', 'start' => 'ASC')
        );

        // Edit form
        $formEdit = $this->createForm(PlanningCollectionType::class, array('elements' => $elements));
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
                $lm->buildFromPlanning($this->getUser()->getCurrentSeason());

                // Flash message
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('planning.success.built', array(), 'lesson')
                );

                // Redirect
                return $this->redirectToRoute('app_lesson_index');

            } else {

                // Flash message
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('planning.success.updated', array(), 'lesson')
                );

                // Redirect
                return $this->redirectToRoute('app_lesson_planning');

            }

        }

        // Render
        return $this->render(
            'lesson/planning.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }
}
