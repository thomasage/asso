<?php
namespace AppBundle\Controller\Stat;

use AppBundle\Entity\Season;
use AppBundle\Form\StatAccountSummaryType;
use AppBundle\Form\StatAmountByThirdType;
use AppBundle\Form\StatMemberSegmentType;
use AppBundle\Form\StatRankProgressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/stat/accountSummary",
     *     name="app_stat_account_summary",
     *     methods={"GET","POST"})
     */
    public function accountSummaryAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Default filter
        $session = $this->get('session');
        if (!$session->has('stat-account-summary-start')
            || !$session->has('stat-account-summary-stop')
        ) {
            $season = $this->getUser()->getCurrentSeason();
            if ($season instanceof Season) {
                $session->set('stat-account-summary-start', $season->getStart());
                $session->set('stat-account-summary-stop', $season->getStop());
            } else {
                $session->set('stat-account-summary-start', new \DateTime(date('Y-m-d', mktime(0, 0, 0, 1, 1))));
                $session->set('stat-account-summary-stop', new \DateTime(date('Y-m-d', mktime(0, 0, 0, 12, 31))));
            }
        }

        // Search form
        $formSearch = $this->createForm(
            StatAccountSummaryType::class,
            [
                'start' => $session->get('stat-account-summary-start'),
                'stop' => $session->get('stat-account-summary-stop'),
            ]
        );
        $formSearch->handleRequest($request);

        // Update filter
        if ($formSearch->isValid() && $formSearch->isSubmitted()) {

            $data = $formSearch->getData();
            $session->set('stat-account-summary-start', $data['start']);
            $session->set('stat-account-summary-stop', $data['stop']);

            return $this->redirectToRoute('app_stat_account_summary');
        }

        // Results
        $results = $em
            ->getRepository('AppBundle:Transaction')
            ->statAccountSummary(
                $session->get('stat-account-summary-start'),
                $session->get('stat-account-summary-stop')
            );

        // Render
        return $this->render(
            'stat/account-summary.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            ]
        );
    }

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
     * @Route("/stat/memberSegment",
     *     name="app_stat_member_segment",
     *     methods={"GET","POST"})
     */
    public function memberSegmentAction(Request $request)
    {
        // Session
        $session = $this->get('session');

        // Default values
        if (!$session->has('stat-member-segment')) {
            $season = $this->getUser()->getCurrentSeason();
            if ($season instanceof Season) {
                $session->set('stat-member-segment', ['season' => $season->getId()]);
            } else {
                $session->set('stat-member-segment', ['season' => null]);
            }
        }

        // Search form
        $formSearch = $this->createForm(
            StatMemberSegmentType::class,
            [
                'season' => $session->get('stat-member-segment')['season'],
            ]
        );
        $formSearch->handleRequest($request);

        // Update filter
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {

            $data = $formSearch->getData();
            $session->set('stat-member-segment', ['season' => $data['season']]);

            return $this->redirectToRoute('app_stat_member_segment');
        }

        // Results
        $em = $this->getDoctrine()->getManager();
        $season = $em->getRepository('AppBundle:Season')->find($session->get('stat-member-segment')['season']);
        if ($season instanceof Season) {
            $results = $em->getRepository('AppBundle:Member')->statSegment($season);
        } else {
            $results = [];
        }

        // Render
        return $this->render(
            'stat/member-segment.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            ]
        );
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
