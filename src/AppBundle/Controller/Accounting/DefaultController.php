<?php
declare(strict_types=1);

namespace AppBundle\Controller\Accounting;

use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionCopy;
use AppBundle\Entity\TransactionDetail;
use AppBundle\Form\Type\TransactionDeleteType;
use AppBundle\Form\Type\TransactionSearchType;
use AppBundle\Form\Type\TransactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/accounting/add",
     *        name="app_accounting_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request): Response
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
            }
            if ($request->request->get('add_and_new') !== null) {
                return $this->redirectToRoute('app_accounting_add');
            }

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
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/accounting/autocompleteBankName",
     *     name="app_accounting_autocomplete_bankname",
     *     methods={"GET"})
     */
    public function autocompleteBankNameAction(Request $request): JsonResponse
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
     *
     * @Route("/accounting/autocompleteCategory",
     *     name="app_accounting_autocomplete_category",
     *     methods={"GET"})
     */
    public function autocompleteCategoryAction(Request $request): JsonResponse
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
     *
     * @Route("/accounting/autocompleteThirdName",
     *     name="app_accounting_autocomplete_thirdname",
     *     methods={"GET"})
     */
    public function autocompleteThirdNameAction(Request $request): JsonResponse
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
     * @return RedirectResponse
     *
     * @Route("/accounting/copy/delete/{copy}",
     *     name="app_accounting_copy_delete",
     *     methods={"GET"},
     *     requirements={"copy"="\d+"})
     */
    public function copyDeleteAction(TransactionCopy $copy): RedirectResponse
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
     * @return Response
     *
     * @Route("/accounting/copy/download/{copy}",
     *        name="app_accounting_copy_download",
     *        methods={"GET"},
     *        requirements={"copy"="\d+"})
     */
    public function copyDownloadAction(TransactionCopy $copy): Response
    {
        $tm = $this->get('app.transaction_manager');

        if (($filename = $tm->getCopyFilename($copy)) !== null) {
            $response = new BinaryFileResponse($filename);
            $response
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $copy->getName())
                ->headers->set('Content-Type', $copy->getMime());

            return $response;
        }

        return new Response(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @param Request $request
     * @param Transaction $transaction
     * @return Response
     *
     * @Route("/accounting/delete/{transaction}",
     *        name="app_accounting_delete",
     *        methods={"GET","POST"},
     *        requirements={"transaction"="\d+"})
     */
    public function deleteAction(Request $request, Transaction $transaction): Response
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
     * @return Response
     *
     * @Route("/accounting/edit/{transaction}",
     *        name="app_accounting_edit",
     *        methods={"GET","POST"},
     *        requirements={"transaction"="\d+"})
     */
    public function editAction(Request $request, Transaction $transaction): Response
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
            }

            return $this->redirectToRoute('app_accounting_homepage');

        }

        // Difference between transaction and breakdown
        if ((float)$transaction->getAmount() !== (float)$transaction->getDetailsAmount()) {
            $this->addFlash(
                'error',
                $this->get('translator')->trans(
                    'edit.warning.breakdown_amount',
                    [
                        '%amount%' => number_format((float)$transaction->getAmount(), 2, '.', ' '),
                        '%breakdown%' => number_format((float)$transaction->getDetailsAmount(), 2, '.', ' '),
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
     * @return Response
     *
     * @Route("/accounting/export",
     *        name="app_accounting_export",
     *        methods={"GET"})
     */
    public function exportAction(): Response
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Categories
        $categories = $em->getRepository(TransactionDetail::class)->findAutocompleteCategory('', null);

        // Transactions
        $transactions = $em->getRepository(Transaction::class)->findBy([], ['date' => 'ASC', 'dateValue' => 'ASC']);

        return $this
            ->get('app.accounting_manager')
            ->export($categories, $transactions);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/accounting",
     *        name="app_accounting_homepage",
     *        methods={"GET","POST"})
     */
    public function indexAction(Request $request): Response
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
