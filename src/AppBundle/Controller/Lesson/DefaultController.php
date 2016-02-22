<?php
namespace AppBundle\Controller\Lesson;

use AppBundle\Entity\Planning;
use AppBundle\Form\PlanningCollectionType;
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
        // Calendar of season
        $start = new \DateTime($this->getUser()->getCurrentSeason()->getStart()->format('Y-m-01'));
        $stop = new \DateTime($this->getUser()->getCurrentSeason()->getStop()->format('Y-m-01'));
        $stop->modify('+1 month');
        $months = array();
        foreach (new \DatePeriod($start, new \DateInterval('P1M'), $stop) as $v) {
            $months[] = $v;
        }

        // Render
        return $this->render(
            'lesson/index.html.twig',
            array(
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
        $elements = array(
            new Planning(),
            new Planning(),
        );

        // Edit form
        $formEdit = $this->createForm(PlanningCollectionType::class, array('elements' => $elements));
        $formEdit->handleRequest($request);

        // Save data
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // TODO : save data
            // TODO : build lessons

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