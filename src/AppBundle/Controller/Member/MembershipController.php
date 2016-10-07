<?php
namespace AppBundle\Controller\Member;

use AppBundle\Entity\Document;
use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Form\DocumentDeleteType;
use AppBundle\Form\MembershipDeleteType;
use AppBundle\Form\MembershipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MembershipController extends Controller
{
    /**
     * @param Request $request
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/membership/add/{member}",
     *        name="app_member_membership_add",
     *        methods={"GET","POST"},
     *        requirements={"member"="\d+"})
     */
    public function addAction(Request $request, Member $member)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $membership = new Membership();
        $membership->setMember($member);
        $membership->setSeason($this->getUser()->getCurrentSeason());
        $formEdit = $this->createForm(MembershipType::class, $membership);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->updateMembership($membership, $formEdit);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('membership_add.success.added', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $member->getId()));

        }

        // Render
        return $this->render(
            'member/membership/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'member' => $member,
            )
        );
    }

    /**
     * @param Request $request
     * @param Membership $membership
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/membership/delete/{membership}",
     *        name="app_member_membership_delete",
     *        methods={"GET","POST"},
     *        requirements={"membership"="\d+"})
     */
    public function deleteAction(Request $request, Membership $membership)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $formDelete = $this->createForm(MembershipDeleteType::class, $membership);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Save data
            $mm->deleteMembership($membership);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('membership_delete.success.deleted', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $membership->getMember()->getId()));

        }

        // Render
        return $this->render(
            'member/membership//delete.html.twig',
            array(
                'formDelete' => $formDelete->createView(),
                'member' => $membership->getMember(),
                'membership' => $membership,
            )
        );
    }

    /**
     * @param Request $request
     * @param Membership $membership
     * @param Document $document
     * @return Response
     *
     * @Route("/member/membership/{membership}/document/{document}/delete",
     *     name="app_member_membership_document_delete",
     *     methods={"GET","POST"},
     *     requirements={"membership"="\d+","document"="\d+"})
     */
    public function documentDeleteAction(Request $request, Membership $membership, Document $document)
    {
        // Delete form
        $formDelete = $this->createForm(DocumentDeleteType::class, $document);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Delete document
            if ($document == $membership->getMedicalCertificate()) {
                $membership->setMedicalCertificate(null);
            } elseif ($document == $membership->getRegistrationForm()) {
                $membership->setRegistrationForm(null);
            }
            $dm = $this->get('app.document_manager');
            $dm->delete($document);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('document_delete.success.deleted', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_membership_edit', ['membership' => $membership->getId()]);

        }

        // Render
        return $this->render(
            'member/membership/document/delete.html.twig',
            [
                'document' => $document,
                'formDelete' => $formDelete->createView(),
                'membership' => $membership,
            ]
        );
    }

    /**
     * @param Document $document
     * @return BinaryFileResponse|NotFoundHttpException
     *
     * @Route("/member/membership/document/{document}",
     *     name="app_member_membership_document_download",
     *     methods={"GET"},
     *     requirements={"document"="\d+"})
     */
    public function documentDownloadAction(Document $document)
    {
        // Document manager
        $dm = $this->get('app.document_manager');

        if (!is_null($filename = $dm->getFilename($document))) {
            $response = new BinaryFileResponse($filename);
            $response
                ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $document->getName())
                ->headers->set('Content-Type', $document->getMime());

            return $response;
        }

        return new NotFoundHttpException();
    }

    /**
     * @param Request $request
     * @param Membership $membership
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/membership/edit/{membership}",
     *        name="app_member_membership_edit",
     *        methods={"GET","POST"},
     *        requirements={"membership"="\d+"})
     */
    public function editAction(Request $request, Membership $membership)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $formEdit = $this->createForm(MembershipType::class, $membership);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->updateMembership($membership, $formEdit);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('membership_edit.success.updated', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $membership->getMember()->getId()));

        }

        // Render
        return $this->render(
            'member/membership/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'member' => $membership->getMember(),
                'membership' => $membership,
            )
        );
    }
}
