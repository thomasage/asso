<?php
namespace AppBundle\Controller\Member;

use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Form\MembershipDeleteType;
use AppBundle\Form\MembershipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $formEdit = $this->createForm(MembershipType::class, $membership);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->updateMembership($membership);

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
            $mm->updateMembership($membership);

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
