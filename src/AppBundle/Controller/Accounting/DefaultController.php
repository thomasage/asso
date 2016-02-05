<?php
namespace AppBundle\Controller\Accounting;

use AppBundle\Entity\Transaction;
use AppBundle\Form\TransactionDeleteType;
use AppBundle\Form\TransactionSearchType;
use AppBundle\Form\TransactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting/add",
     *        name="app_accounting_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        // Edit form
        $transaction = new Transaction();
        $transaction->setDate(new \DateTime());
        $formEdit = $this->createForm(TransactionType::class, $transaction);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('add.success.added', array(), 'accounting'));

            // Redirect
            return $this->redirectToRoute('app_accounting_edit', array('transaction' => $transaction->getId()));

        }

        // Render
        return $this->render(
            'accounting/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting/delete/{transaction}",
     *        name="app_accounting_delete",
     *        methods={"GET","POST"},
     *        requirements={"transaction"="\d+"})
     */
    public function deleteAction(Request $request, Transaction $transaction)
    {
        // Delete form
        $formDelete = $this->createForm(TransactionDeleteType::class, $transaction);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Delete data
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('delete.success.deleted', array(), 'accounting')
            );

            // Redirect
            return $this->redirectToRoute('app_accounting_homepage');

        }

        // Render
        return $this->render(
            'accounting/delete.html.twig',
            array(
                'formDelete' => $formDelete->createView(),
                'transaction' => $transaction,
            )
        );
    }

    /**
     * @param Request $request
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting/edit/{transaction}",
     *        name="app_accounting_edit",
     *        methods={"GET","POST"},
     *        requirements={"transaction"="\d+"})
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        // Edit form
        $formEdit = $this->createForm(TransactionType::class, $transaction);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('edit.success.updated', array(), 'accounting'));

            // Redirect
            if ($request->request->get('update')) {
                return $this->redirectToRoute('app_accounting_edit', array('transaction' => $transaction->getId()));
            } else {
                return $this->redirectToRoute('app_accounting_homepage');
            }

        }

        // Render
        return $this->render(
            'accounting/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'transaction' => $transaction,
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/accounting",
     *        name="app_accounting_homepage",
     *        methods={"GET","POST"})
     */
    public function indexAction(Request $request)
    {
        $route = 'app_accounting_homepage';

        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Search form
        $formSearch = $this->createForm(TransactionSearchType::class);

        // Search manager
        $sm = $this->get('app.search_manager');
        $search = $sm->get($route, $this->getUser());
        $reload = $search->handleRequest($request, $formSearch);
        if ($reload) {
            $sm->update($search);

            return $this->redirectToRoute('app_accounting_homepage');
        }

        // Transactions
        $transactions = $em->getRepository('AppBundle:Transaction')->findBySearch($search);

        // Render
        return $this->render(
            'accounting/index.html.twig',
            array(
                'formSearch' => $formSearch->createView(),
                'route' => $route,
                'transactions' => $transactions,
            )
        );
    }
}