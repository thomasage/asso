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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
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
            if ($request->request->get('add_and_close') !== null) {
                return $this->redirectToRoute('app_accounting_homepage');
            } elseif ($request->request->get('add_and_new') !== null) {
                return $this->redirectToRoute('app_accounting_add');
            } else {
                return $this->redirectToRoute('app_accounting_edit', ['transaction' => $transaction->getId()]);
            }

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
     * @param Request $request
     * @return JsonResponse
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/accounting/autocompleteBankName",
     *     name="app_accounting_autocomplete_bankname",
     *     methods={"GET"},
     *     options={"expose"=true})
     */
    public function autocompleteBankNameAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Results
        $data = $em->getRepository(Transaction::class)->findAutocompleteBankName($request->query->get('term'));

        // Render
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/accounting/autocompleteCategory",
     *     name="app_accounting_autocomplete_category",
     *     methods={"GET"},
     *     options={"expose"=true})
     */
    public function autocompleteCategoryAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Results
        $data = $em->getRepository(TransactionDetail::class)->findAutocompleteCategory($request->query->get('term'));

        // Render
        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/accounting/autocompleteThirdName",
     *     name="app_accounting_autocomplete_thirdname",
     *     methods={"GET"},
     *     options={"expose"=true})
     */
    public function autocompleteThirdNameAction(Request $request)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Results
        $data = $em->getRepository(Transaction::class)->findAutocompleteThirdName($request->query->get('term'));

        // Render
        return new JsonResponse($data);
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

        if (($filename = $tm->getCopyFilename($copy)) !== null) {
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
     * @throws \LogicException
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
     * @throws \LogicException
     * @throws \InvalidArgumentException
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
        $copy = $em->getRepository(TransactionCopy::class)->findOneBy(['transaction' => $transaction]);

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
            if ($request->request->get('update') !== null) {
                return $this->redirectToRoute('app_accounting_edit', ['transaction' => $transaction->getId()]);
            } else {
                return $this->redirectToRoute('app_accounting_homepage');
            }

        }

        // Difference between transaction and breakdown
        if ($transaction->getAmount() !== $transaction->getDetailsAmount()) {
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     *
     * @Route("/accounting/export",
     *        name="app_accounting_export",
     *        methods={"GET"})
     */
    public function exportAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Categories
        $categories = $em->getRepository(TransactionDetail::class)->findAutocompleteCategory('');

        // Transactions
        $transactions = $em->getRepository(Transaction::class)->findBy([], ['date' => 'ASC']);

        // Render
        return new Response(
            $this->renderView(
                'accounting/index.xlsx.php',
                [
                    'categories' => $categories,
                    'transactions' => $transactions,
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
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
        $transactions = $em->getRepository(Transaction::class)->findBySearch($search);

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
