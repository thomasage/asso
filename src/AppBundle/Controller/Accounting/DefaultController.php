<?php
namespace AppBundle\Controller\Accounting;

use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionCopy;
use AppBundle\Entity\TransactionDetail;
use AppBundle\Form\TransactionDeleteType;
use AppBundle\Form\TransactionSearchType;
use AppBundle\Form\TransactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // Transaction
        $transaction = new Transaction();
        $transaction->addDetail(new TransactionDetail());

        // Edit form
        $formEdit = $this->createForm(TransactionType::class, $transaction);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $tm = $this->get('app.transaction_manager');
            $tm->update($transaction, $formEdit);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('add.success.added', [], 'accounting'));

            // Redirect
            return $this->redirectToRoute('app_accounting_edit', ['transaction' => $transaction->getId()]);

        }

        // Render
        return $this->render(
            'accounting/add.html.twig',
            [
                'formEdit' => $formEdit->createView(),
            ]
        );
    }

    /**
     * @param TransactionCopy $copy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * 
     * @Route("/accounting/copy/delete/{copy}",
     *     name="app_accounting_copy_delete",
     *     methods={"GET"},
     *     requirements={"copy"="\d+"},
     *     options={"expose"=true})
     */
    public function copyDeleteAction(TransactionCopy $copy)
    {
        $transaction = $copy->getTransaction();

        $tm = $this->get('app.transaction_manager');

        $tm->copyDelete($copy);

        return $this->redirectToRoute(
            'app_accounting_edit',
            [
                'transaction' => $transaction->getId(),
            ]
        );
    }

    /**
     * @param TransactionCopy $copy
     * @return BinaryFileResponse|NotFoundHttpException
     *
     * @Route("/accounting/copy/download/{copy}",
     *        name="app_accounting_copy_download",
     *        methods={"GET"},
     *        requirements={"copy"="\d+"})
     */
    public function copyDownloadAction(TransactionCopy $copy)
    {
        $tm = $this->get('app.transaction_manager');

        if (!is_null($filename = $tm->getCopyFilename($copy))) {
            $response = new BinaryFileResponse($filename);
            $response
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $copy->getName())
                ->headers->set('Content-Type', $copy->getMime());

            return $response;
        }

        return new NotFoundHttpException();
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
            $tm = $this->get('app.transaction_manager');
            $tm->delete($transaction);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('delete.success.deleted', [], 'accounting')
            );

            // Redirect
            return $this->redirectToRoute('app_accounting_homepage');

        }

        // Render
        return $this->render(
            'accounting/delete.html.twig',
            [
                'formDelete' => $formDelete->createView(),
                'transaction' => $transaction,
            ]
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
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Copy
        $copy = $em->getRepository('AppBundle:TransactionCopy')->findOneBy(['transaction' => $transaction]);

        // Edit form
        $formEdit = $this->createForm(TransactionType::class, $transaction);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $tm = $this->get('app.transaction_manager');
            $tm->update($transaction, $formEdit);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('edit.success.updated', [], 'accounting'));

            // Redirect
            if (!is_null($request->request->get('update'))) {
                return $this->redirectToRoute('app_accounting_edit', ['transaction' => $transaction->getId()]);
            } else {
                return $this->redirectToRoute('app_accounting_homepage');
            }

        }

        // Difference between transaction and breakdown
        if ($transaction->getAmount() != $transaction->getDetailsAmount()) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans(
                    'edit.warning.breakdown_amount',
                    [
                        '%amount%' => number_format($transaction->getAmount(), 2, '.', ' '),
                        '%breakdown%' => number_format($transaction->getDetailsAmount(), 2, '.', ' '),
                    ],
                    'accounting'
                )
            );
        }

        // Render
        return $this->render(
            'accounting/edit.html.twig',
            [
                'copy' => $copy,
                'formEdit' => $formEdit->createView(),
                'transaction' => $transaction,
            ]
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
            [
                'formSearch' => $formSearch->createView(),
                'route' => $route,
                'transactions' => $transactions,
            ]
        );
    }
}
