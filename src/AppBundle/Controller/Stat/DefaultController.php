<?php
namespace AppBundle\Controller\Stat;

use AppBundle\Form\StatAmountByThirdType;
use AppBundle\Form\StatRankProgressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/stat/amountByMonth",
     *     name="app_stat_amount_by_month",
     *     methods={"GET"})
     */
    public function amountByMonthAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Resuls
        $results = $em->getRepository('AppBundle:Transaction')->statAmountByMonth();
        
        return $this->render(
            'stat/amount-by-month.html.twig',
            [
                'results' => $results,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/amountByThird",
     *        name="app_stat_amount_by_third",
     *        methods={"GET","POST"})
     */
    public function amountByThirdAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Curret season
        $season = $this->getUser()->getCurrentSeason();

        // Search form
        $formSearch = $this->createForm(
            StatAmountByThirdType::class,
            [
                'start' => $season->getStart(),
                'stop' => $season->getStop(),
            ]
        );
        $formSearch->handleRequest($request);

        $results = $em->getRepository('AppBundle:Transaction')->statAmountByThird(
            $formSearch->get('start')->getData(),
            $formSearch->get('stop')->getData()
        );

        // Render
        return $this->render(
            'stat/amount-by-third.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            ]
        );
    }

    /**
     * @return Response
     *
     * @Route("/stat",
     *        name="app_stat_index",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Render
        return $this->render('stat/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/rankProgress",
     *        name="app_stat_rank_progress",
     *        methods={"GET","POST"})
     */
    public function rankProgressAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Search form
        $formSearch = $this->createForm(
            StatRankProgressType::class,
            [
                'season' => $this->getUser()->getCurrentSeason()->getId(),
            ]
        );
        $formSearch->handleRequest($request);

        $season = $em->getRepository('AppBundle:Season')->find($formSearch->getData()['season']);
        $results = $em->getRepository('AppBundle:Member')->statRankProgress($season);

        // Render
        return $this->render(
            'stat/rank.progress.html.twig',
            array(
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            )
        );
    }
}
