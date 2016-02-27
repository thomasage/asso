<?php
namespace AppBundle\Controller\Member;

use AppBundle\Entity\Member;
use AppBundle\Entity\Promotion;
use AppBundle\Form\PromotionDeleteType;
use AppBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PromotionController extends Controller
{
    /**
     * @param Request $request
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/promotion/add/{member}",
     *        name="app_member_promotion_add",
     *        methods={"GET","POST"},
     *        requirements={"member"="\d+"})
     */
    public function addAction(Request $request, Member $member)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $promotion = new Promotion();
        $promotion->setMember($member);
        $promotion->setDate(new \DateTime());
        $formEdit = $this->createForm(PromotionType::class, $promotion);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->updatePromotion($promotion);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('promotion_add.success.added', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $member->getId()));

        }

        // Render
        return $this->render(
            'member/promotion/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'member' => $member,
            )
        );
    }

    /**
     * @param Request $request
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/promotion/delete/{promotion}",
     *        name="app_member_promotion_delete",
     *        methods={"GET","POST"},
     *        requirements={"promotion"="\d+"})
     */
    public function deleteAction(Request $request, Promotion $promotion)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $formDelete = $this->createForm(PromotionDeleteType::class, $promotion);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Save data
            $mm->deletePromotion($promotion);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('promotion_delete.success.deleted', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $promotion->getMember()->getId()));

        }

        // Render
        return $this->render(
            'member/promotion/delete.html.twig',
            array(
                'formDelete' => $formDelete->createView(),
                'member' => $promotion->getMember(),
                'promotion' => $promotion,
            )
        );
    }

    /**
     * @param Request $request
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/promotion/edit/{promotion}",
     *        name="app_member_promotion_edit",
     *        methods={"GET","POST"},
     *        requirements={"promotion"="\d+"})
     */
    public function editAction(Request $request, Promotion $promotion)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $formEdit = $this->createForm(PromotionType::class, $promotion);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->updatePromotion($promotion);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('promotion_edit.success.updated', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_show', array('member' => $promotion->getMember()->getId()));

        }

        // Render
        return $this->render(
            'member/promotion/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'member' => $promotion->getMember(),
                'promotion' => $promotion,
            )
        );
    }
}
