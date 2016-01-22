<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Member;
use AppBundle\Entity\Promotion;
use AppBundle\Form\MemberSearchType;
use AppBundle\Form\MemberType;
use AppBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class MemberController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/member/add",
     *        name="app_member_add")
     */
    public function addAction(Request $request)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $member = new Member();
        $formEdit = $this->createForm(MemberType::class, $member);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->update($member);

            // Save photo
            $photo = $formEdit->get('photo')->getData();
            if ($photo instanceof UploadedFile) {
                $mm->updatePhoto($member, $photo);
            }

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('add.success.added', array(), 'member'));

            // Redirect
            return $this->redirectToRoute('app_member_edit', array('member' => $member->getId()));

        }

        // Render
        return $this->render('member/add.html.twig', array('formEdit' => $formEdit->createView()));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/member/edit/{member}",
     *        name="app_member_edit",
     *        requirements={"member"="\d+"})
     */
    public function editAction(Request $request, Member $member)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        $formEdit = $this->createForm(MemberType::class, $member);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $mm->update($member);

            // Save photo
            $photo = $formEdit->get('photo')->getData();
            if ($photo instanceof UploadedFile) {
                $mm->updatePhoto($member, $photo);
            }

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('edit.success.updated', array(), 'member'));

            // Redirect
            if ($request->request->has('update_close')) {
                return $this->redirectToRoute('app_member_show', array('member' => $member->getId()));
            } else {
                return $this->redirectToRoute('app_member_edit', array('member' => $member->getId()));
            }

        }

        if ($request->query->get('delete') == 'photo') {

            // Remove photo
            $mm->deletePhoto($member);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('edit.success.photo_deleted', array(), 'member')
            );

            // Redirect
            return $this->redirectToRoute('app_member_edit', array('member' => $member->getId()));

        }

        // Render
        return $this->render(
            'member/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'member' => $member,
            )
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/member",
     *        name="app_member_index")
     */
    public function indexAction(Request $request)
    {
        $route = 'app_member_index';

        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Search form
        $formSearch = $this->createForm(MemberSearchType::class);

        // Search manager
        $sm = $this->get('app.search_manager');
        $search = $sm->get($route, $this->getUser());
        $reload = $search->handleRequest($request, $formSearch);
        if ($reload) {
            $sm->update($search);

            return $this->redirectToRoute('app_member_index');
        }

        // Members
        $members = $em->getRepository('AppBundle:Member')->findBySearch($search);

        // Render
        return $this->render(
            'member/index.html.twig',
            array(
                'formSearch' => $formSearch->createView(),
                'members' => $members,
                'route' => $route,
            )
        );
    }

    /**
     * @param Member $member
     * @return BinaryFileResponse
     *
     * @Route("/member/photo/{member}",
     *        name="app_member_photo",
     *        requirements={"member"="\d+"})
     */
    public function photoAction(Member $member)
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        // Photo
        $photo = $mm->getPhoto($member);

        if (!is_null($photo)) {
            return new BinaryFileResponse($photo);
        }

        if ($member->getGender() == 'f') {
            return new BinaryFileResponse($this->container->getParameter('member_photo_directory').'/female.png');
        } else {
            return new BinaryFileResponse($this->container->getParameter('member_photo_directory').'/male.png');
        }
    }

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
    public function promotionAddAction(Request $request, Member $member)
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
     * @Route("/member/promotion/edit/{promotion}",
     *        name="app_member_promotion_edit",
     *        methods={"GET","POST"},
     *        requirements={"promotion"="\d+"})
     */
    public function promotionEditAction(Request $request, Promotion $promotion)
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
            )
        );
    }

    /**
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/member/show/{member}",
     *        name="app_member_show",
     *        requirements={"member"="\d+"})
     */
    public function showAction(Member $member)
    {
        $mm = $this->get('app.member_manager');

        $promotions = $mm->getPromotions($member);

        // Render
        return $this->render(
            'member/show.html.twig',
            array(
                'member' => $member,
                'promotions' => $promotions,
            )
        );
    }
}