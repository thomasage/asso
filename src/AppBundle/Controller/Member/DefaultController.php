<?php
namespace AppBundle\Controller\Member;

use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Membership;
use AppBundle\Form\MemberSearchType;
use AppBundle\Form\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
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
            if ($request->request->has('add_and_new')) {
                return $this->redirectToRoute('app_member_add');
            } else {
                return $this->redirectToRoute('app_member_edit', array('member' => $member->getId()));
            }

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
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/member/show/{member}",
     *        name="app_member_show",
     *        requirements={"member"="\d+"})
     */
    public function showAction(Member $member)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Member manager
        $mm = $this->get('app.member_manager');

        $promotions = $mm->getPromotions($member);

        $memberships = $mm->getMemberships($member);

        // Season to display
        $season = $this->getUser()->getCurrentSeason();

        // Membership for the season
        $currentMembership = $em->getRepository('AppBundle:Membership')->findOneBy(
            array('member' => $member, 'season' => $season)
        );

        // Lessons for the season and level
        if ($currentMembership instanceof Membership && $currentMembership->getLevel() instanceof Level) {
            $lessons = $em->getRepository('AppBundle:Lesson')->findByLevelAndSeason(
                $currentMembership->getLevel(),
                $season
            );
        } else {
            $lessons = array();
        }

        // Render
        return $this->render(
            'member/show.html.twig',
            array(
                'lessons' => $lessons,
                'member' => $member,
                'memberships' => $memberships,
                'promotions' => $promotions,
            )
        );
    }

    /**
     * @return Response
     */
    public function widgetNextBirthdaysAction()
    {
        // Member manager
        $mm = $this->get('app.member_manager');

        // Next birthdays
        $members = $mm->getNextBirthdays();

        // Render
        return $this->render(
            'member/widget/_next_birthdays.html.twig',
            array(
                'members' => $members,
                'now' => new \DateTime(),
            )
        );
    }
}
