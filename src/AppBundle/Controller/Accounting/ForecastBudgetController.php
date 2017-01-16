<?php
namespace AppBundle\Controller\Accounting;

use AppBundle\Entity\ForecastBudgetItem;
use AppBundle\Entity\ForecastBudgetPeriod;
use AppBundle\Form\ForecastBudgetItemCollectionType;
use AppBundle\Form\ForecastBudgetPeriodType;
use AppBundle\Form\ForecastBudgetSearchType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ForecastBudgetController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     *
     * @Route("/accounting/forecast-budget/period/add",
     *        name="app_accounting_forecast_budget_period_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        // Period
        $period = new ForecastBudgetPeriod();

        // Edit form
        $formEdit = $this->createForm(ForecastBudgetPeriodType::class, $period);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $em = $this->getDoctrine()->getManager();
            $em->persist($period);
            $em->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('foreacast_budget_period.success.added', [], 'accounting')
            );

            // Redirect
            return $this->redirectToRoute('app_accounting_forecast_budget');

        }

        // Render
        return $this->render(
            'accounting/forecast-budget/period.add.html.twig',
            [
                'formEdit' => $formEdit->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @Route("/accounting/forecast-budget",
     *        name="app_accounting_forecast_budget",
     *        methods={"GET","POST"})
     */
    public function indexAction(Request $request)
    {
        // Check if at least one period already exists
        $em = $this->getDoctrine()->getManager();
        $periods = $em->getRepository(ForecastBudgetPeriod::class)->findBy([], ['start' => 'DESC']);
        if (count($periods) === 0) {
            return $this->redirectToRoute('app_accounting_forecast_budget_period_add');
        }

        // Default period
        $session = $this->get('session');
        if (!$session->has('forecast_budget_period')) {
            $session->set('forecast_budget_period', $periods[0]->getId());
        }

        // Search form
        $formSearch = $this->createForm(
            ForecastBudgetSearchType::class,
            [
                'period' => $session->get('forecast_budget_period'),
            ]
        );
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $session->set('forecast_budget_period', $formSearch->get('period')->getData());

            return $this->redirectToRoute('app_accounting_forecast_budget');
        }

        // Items of period
        $items = $em
            ->getRepository(ForecastBudgetItem::class)
            ->findBy(
                ['period' => $session->get('forecast_budget_period')],
                ['category' => 'ASC']
            );

        // Edit form
        $formEdit = $this->createForm(ForecastBudgetItemCollectionType::class, ['items' => $items]);
        $formEdit->handleRequest($request);
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            $period = $em->getRepository(ForecastBudgetPeriod::class)->find($session->get('forecast_budget_period'));

            $new = new ArrayCollection($formEdit->get('items')->getData());
            foreach ($new as $item) {
                if (!$item instanceof ForecastBudgetItem) {
                    continue;
                }
                $item->setPeriod($period);
                $em->persist($item);
            }

            foreach ($items as $item) {
                if (!$new->contains($item)) {
                    $em->remove($item);
                }
            }

            $em->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('foreacast_budget_items.success.updated', [], 'accounting')
            );

            // Redirect
            return $this->redirectToRoute('app_accounting_forecast_budget');
        }

        // Render
        return $this->render(
            'accounting/forecast-budget/index.html.twig',
            [
                'formEdit' => $formEdit->createView(),
                'formSearch' => $formSearch->createView(),
            ]
        );
    }
}
