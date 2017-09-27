<?php

namespace AppBundle\Controller\Stat;

use AppBundle\Entity\Attendance;
use AppBundle\Entity\ForecastBudgetItem;
use AppBundle\Entity\ForecastBudgetPeriod;
use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\Season;
use AppBundle\Entity\Transaction;
use AppBundle\Form\Type\StatAccountSummaryType;
use AppBundle\Form\Type\StatAmountByThirdType;
use AppBundle\Form\Type\StatAttendanceLessonType;
use AppBundle\Form\Type\StatAttendanceType;
use AppBundle\Form\Type\StatForecastBudgetType;
use AppBundle\Form\Type\StatLastPromotionType;
use AppBundle\Form\Type\StatMemberOriginType;
use AppBundle\Form\Type\StatMemberSegmentType;
use AppBundle\Form\Type\StatMemberSignatureType;
use AppBundle\Form\Type\StatRankProgressType;
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
    public function accountSummaryAction(Request $request): Response
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
     *
     * @Route("/stat/amountByMonth",
     *     name="app_stat_amount_by_month",
     *     methods={"GET"})
     */
    public function amountByMonthAction(): Response
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
     *
     * @Route("/stat/amountByThird",
     *        name="app_stat_amount_by_third",
     *        methods={"GET","POST"})
     */
    public function amountByThirdAction(Request $request): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Current season
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
     *
     * @Route("/stat/attendance",
     *        name="app_stat_attendance",
     *        methods={"GET","POST"})
     */
    public function attendanceAction(Request $request): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Current season
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

        }

        return $this->render(
            'stat/attendance.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'lessons' => $lessons,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/last-promotion",
     *        name="app_stat_last_promotion",
     *        methods={"GET", "POST"})
     */
    public function lastPromotionAction(Request $request): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $route = 'app_stat_last_promotion';

        // Search form
        $formSearch = $this->createForm(StatLastPromotionType::class);

        // Search manager
        $sm = $this->get('app.search_manager');
        $search = $sm->get($route, $this->getUser());
        $reload = $search->handleRequest($request, $formSearch);
        if ($reload) {
            $sm->update($search);

            return $this->redirectToRoute($route);
        }

        $season = $em->getRepository(Season::class)->find($search->getFilter('season') ?? 0);
        if (!$season instanceof Season) {
            $season = $em->getRepository(Season::class)->findOneBy([]);
            if (!$season instanceof Season) {
                return $this->redirectToRoute('app_stat_index');
            }
            $search->addFilter('season', $season->getId());
            $sm->update($search);
        }

        $promotions = $em
            ->getRepository(Promotion::class)
            ->findBySeason($em->getRepository(Season::class)->find($search->getFilter('season')));

        // Render
        return $this->render(
            'stat/last_promotion.html.twig',
            [
                'formSearch' => $formSearch->createView(),
                'promotions' => $promotions,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/attendance-lesson",
     *        name="app_stat_attendance_lesson",
     *        methods={"GET", "POST"})
     */
    public function attendanceLessonAction(Request $request): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $route = 'app_stat_attendance_lesson';

        // Search form
        $formSearch = $this->createForm(StatAttendanceLessonType::class);

        // Search manager
        $sm = $this->get('app.search_manager');
        $search = $sm->get($route, $this->getUser());
        $reload = $search->handleRequest($request, $formSearch);
        if ($reload) {
            $sm->update($search);

            return $this->redirectToRoute('app_stat_attendance_lesson');
        }

        $level = $em->getRepository(Level::class)->find($search->getFilter('level') ?? 0);
        if (!$level instanceof Level) {
            $level = $em->getRepository(Level::class)->findOneBy([]);
            if (!$level instanceof Level) {
                return $this->redirectToRoute('app_stat_index');
            }
            $search->addFilter('level', $level->getId());
            $sm->update($search);
        }
        $season = $em->getRepository(Season::class)->find($search->getFilter('season') ?? 0);
        if (!$season instanceof Season) {
            $season = $em->getRepository(Season::class)->findOneBy([]);
            if (!$season instanceof Season) {
                return $this->redirectToRoute('app_stat_index');
            }
            $search->addFilter('season', $season->getId());
            $sm->update($search);
        }

        $data = $em->getRepository(Attendance::class)->statByLesson(
            $em->getRepository(Season::class)->find($search->getFilter('season')),
            $em->getRepository(Level::class)->find($search->getFilter('level'))
        );

        // Render
        return $this->render(
            'stat/attendance_lesson.html.twig',
            [
                'data' => $data,
                'formSearch' => $formSearch->createView(),
            ]
        );
    }

    /**
     * @return Response
     *
     * @Route("/stat/attendance-week",
     *        name="app_stat_attendance_week",
     *        methods={"GET"})
     */
    public function attendanceWeekAction(): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Data
        $data = $em->getRepository(Attendance::class)->statByWeek();

        $seasons = [];
        $series = [];
        foreach ($data as $d) {
            $index = array_search($d['season'], $seasons, true);
            if ($index === false) {
                $index = count($series);
                $seasons[$index] = $d['season'];
                $series[$index] = (object)[
                    'name' => $d['season'],
                    'data' => [],
                ];
            }
            $series[$index]->data[] = (int)$d['members'];
        }

        // Render
        return $this->render(
            'stat/attendance_week.html.twig',
            [
                'series' => $series,
            ]
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/forecast-budget",
     *        name="app_stat_forecast_budget",
     *        methods={"GET","POST"})
     */
    public function forecastBudgetAction(Request $request): Response
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
                }
                $period = $period[0];
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
    public function indexAction(): Response
    {
        // Render
        return $this->render('stat/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/stat/memberOrigin",
     *     name="app_stat_member_origin",
     *     methods={"GET","POST"})
     */
    public function memberOriginAction(Request $request): Response
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
     *
     * @Route("/stat/memberEvolution",
     *     name="app_stat_member_evolution",
     *     methods={"GET"})
     */
    public function memberEvolutionAction(): Response
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
     *
     * @Route("/stat/memberSegment",
     *     name="app_stat_member_segment",
     *     methods={"GET","POST"})
     */
    public function memberSegmentAction(Request $request): Response
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
     *
     * @Route("/stat/memberSignature",
     *     name="app_stat_member_signature",
     *     methods={"GET"})
     */
    public function memberSignatureAction(Request $request): Response
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
            ['season' => $session->get('stat-member-signature')['season']],
            ['method' => 'GET']
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
     *
     * @Route("/stat/rankProgress",
     *        name="app_stat_rank_progress",
     *        methods={"GET"})
     */
    public function rankProgressAction(Request $request): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Search form
        $formSearch = $this->createForm(
            StatRankProgressType::class,
            ['season' => $this->getUser()->getCurrentSeason()->getId()],
            ['method' => 'GET']
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
            [
                'formSearch' => $formSearch->createView(),
                'results' => $results,
            ]
        );
    }
}
