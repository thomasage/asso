<?php
namespace AppBundle\Controller\Stat;

use AppBundle\Entity\ForecastBudgetItem;
use AppBundle\Entity\ForecastBudgetPeriod;
use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Season;
use AppBundle\Entity\Transaction;
use AppBundle\Form\StatAccountSummaryType;
use AppBundle\Form\StatAmountByThirdType;
use AppBundle\Form\StatAttendanceType;
use AppBundle\Form\StatForecastBudgetType;
use AppBundle\Form\StatMemberOriginType;
use AppBundle\Form\StatMemberSegmentType;
use AppBundle\Form\StatMemberSignatureType;
use AppBundle\Form\StatRankProgressType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws NonUniqueResultException
     * @throws NoResultException
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
            ->getRepository(Transaction::class)
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
     * @throws \LogicException
     * @throws \InvalidArgumentException
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
        $results = $em->getRepository(Transaction::class)->statAmountByMonth();

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
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
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
        if (!$season instanceof Season) {
            return $this->redirectToRoute('app_stat_index');
        }

        // Search form
        $formSearch = $this->createForm(
            StatAmountByThirdType::class,
            [
                'start' => $season->getStart(),
                'stop' => $season->getStop(),
            ]
        );
        $formSearch->handleRequest($request);

        $results = $em->getRepository(Transaction::class)->statAmountByThird(
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
     * @param Request $request
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @Route("/stat/attendance",
     *        name="app_stat_attendance",
     *        methods={"GET","POST"})
     */
    public function attendanceAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Curret season
        $season = $this->getUser()->getCurrentSeason();
        if (!$season instanceof Season) {
            return $this->redirectToRoute('app_stat_index');
        }

        // Search form
        $formSearch = $this->createForm(
            StatAttendanceType::class,
            [
                'season' => $season->getId(),
            ]
        );
        $formSearch->handleRequest($request);

        $season = $em->getRepository(Season::class)->find($formSearch->get('season')->getData());
        $lessons = $em->getRepository(Lesson::class)->statAttendance($season);

        // Render
        if ($request->request->has('ods')) {

            return new Response(
                $this->renderView(
                    'stat/attendance.ods.php',
                    [
                        'lessons' => $lessons,
                        'translator' => $this->get('translator'),
                    ]
                ),
                Response::HTTP_OK
            );

        } else {

            return $this->render(
                'stat/attendance.html.twig',
                [
                    'formSearch' => $formSearch->createView(),
                    'lessons' => $lessons,
                ]
            );

        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws NonUniqueResultException
     * @throws \OutOfBoundsException
     *
     * @Route("/stat/forecast-budget",
     *        name="app_stat_forecast_budget",
     *        methods={"GET","POST"})
     */
    public function forecastBudgetAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Session
        $session = $this->get('session');
        if (!$session->has('stat_forecast_budget_period')) {
            $period = $em->getRepository(ForecastBudgetPeriod::class)->findCurrent();
            if (!$period instanceof ForecastBudgetPeriod) {
                $period = $em->getRepository(ForecastBudgetPeriod::class)->findBy([], ['start' => 'DESC']);
                if (count($period) === 0) {
                    return $this->redirectToRoute('app_stat_index');
                } else {
                    $period = $period[0];
                }
            }
            $session->set('stat_forecast_budget_period', $period);
        }

        // Search form
        $formSearch = $this
            ->createForm(
                StatForecastBudgetType::class,
                [
                    'period' => $session->get('stat_forecast_budget_period')->getId(),
                ]
            );
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $period = $em->getRepository(ForecastBudgetPeriod::class)->find($formSearch->get('period')->getData());
            $session->set('stat_forecast_budget_period', $period);

            return $this->redirectToRoute('app_stat_forecast_budget');
        }

        $expenses = $em
            ->getRepository(ForecastBudgetItem::class)
            ->statExpenses($session->get('stat_forecast_budget_period'));

        $receipts = $em
            ->getRepository(ForecastBudgetItem::class)
            ->statReceipts($session->get('stat_forecast_budget_period'));

        // Render
        return $this->render(
            'stat/forecast-budget.html.twig',
            [
                'expenses' => $expenses,
                'formSearch' => $formSearch->createView(),
                'receipts' => $receipts,
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
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/stat/memberOrigin",
     *     name="app_stat_member_origin",
     *     methods={"GET","POST"})
     */
    public function memberOriginAction(Request $request)
    {
        // Session
        $session = $this->get('session');

        // Default values
        if (!$session->has('stat-member-origin')) {
            $season = $this->getUser()->getCurrentSeason();
            if ($season instanceof Season) {
                $session->set('stat-member-origin', ['season' => $season->getId()]);
            } else {
                $session->set('stat-member-origin', ['season' => null]);
            }
        }

        // Search form
        $formSearch = $this->createForm(
            StatMemberOriginType::class,
            [
                'season' => $session->get('stat-member-origin')['season'],
            ]
        );
        $formSearch->handleRequest($request);

        // Update filter
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {

            $data = $formSearch->getData();
            $session->set('stat-member-origin', ['season' => $data['season']]);

            return $this->redirectToRoute('app_stat_member_origin');
        }

        // Results
        $em = $this->getDoctrine()->getManager();
        $season = $em->getRepository(Season::class)->find($session->get('stat-member-origin')['season']);
        $results = [];
        if ($season instanceof Season) {
            $results = $em->getRepository(Member::class)->statOrigin($season);
        }

        // Render
        return $this->render(
            'stat/member-origin.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            ]
        );
    }

    /**
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/stat/memberEvolution",
     *     name="app_stat_member_evolution",
     *     methods={"GET"})
     */
    public function memberEvolutionAction()
    {
        // Results
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository(Member::class)->statEvolution();

        // Render
        return $this->render(
            'stat/member-evolution.html.twig',
            [
                'results' => $results,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
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
        $season = $em->getRepository(Season::class)->find($session->get('stat-member-segment')['season']);
        $results = [];
        if ($season instanceof Season) {
            $results = $em->getRepository(Member::class)->statSegment($season);
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
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @Route("/stat/memberSignature",
     *     name="app_stat_member_signature",
     *     methods={"GET","POST"})
     */
    public function memberSignatureAction(Request $request)
    {
        // Session
        $session = $this->get('session');

        // Default values
        if (!$session->has('stat-member-signature')) {
            $season = $this->getUser()->getCurrentSeason();
            if ($season instanceof Season) {
                $session->set('stat-member-signature', ['season' => $season->getId()]);
            } else {
                $session->set('stat-member-signature', ['season' => null]);
            }
        }

        // Search form
        $formSearch = $this->createForm(
            StatMemberSignatureType::class,
            [
                'season' => $session->get('stat-member-signature')['season'],
            ]
        );
        $formSearch->handleRequest($request);

        // Update filter
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {

            $data = $formSearch->getData();
            $session->set('stat-member-signature', ['season' => $data['season']]);

            // Results
            $em = $this->getDoctrine()->getManager();
            $season = $em->getRepository(Season::class)->find($session->get('stat-member-signature')['season']);
            $results = [];
            if ($season instanceof Season) {
                $results = $em->getRepository(Member::class)->statSignature($season);
            }

            // Render PDF
            return new Response(
                $this->renderView(
                    'stat/member-signature.pdf.php',
                    [
                        'applicationName' => $this->getParameter('application_name'),
                        'results' => $results,
                        'documentTitle' => $formSearch->get('title')->getData(),
                    ]
                ),
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/pdf',
                ]
            );
        }

        // Render
        return $this->render(
            'stat/member-signature.html.twig',
            [
                'formSearch' => $formSearch->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
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

        $data = $formSearch->getData();
        $season = $em->getRepository(Season::class)->find($data['season']);
        $level = null;
        if (isset($data['level'])) {
            $level = $em->getRepository(Level::class)->find($data['level']);
        }
        $results = $em->getRepository(Member::class)->statRankProgress($season, $level);

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
