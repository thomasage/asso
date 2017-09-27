<?php
namespace AppBundle\Controller\Param;

use AppBundle\Entity\Rank;
use AppBundle\Form\Type\RankType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RankController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/rank/add",
     *        name="app_param_rank_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        $rank = new Rank();

        $formEdit = $this->createForm(RankType::class, $rank);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.rank_manager');
            $rm->updateRank($rank);
            $image = $formEdit->get('image')->getData();
            if ($image instanceof UploadedFile) {
                $rm->updateImage($rank, $image);
            }

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('rank_add.success.added', array(), 'param'));

            // Redirect
            return $this->redirectToRoute('app_param_rank');

        }

        // Render
        return $this->render(
            'param/rank/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Rank $rank
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/rank/edit/{rank}",
     *        name="app_param_rank_edit",
     *        methods={"GET","POST"},
     *        requirements={"rank"="\d+"})
     */
    public function editAction(Request $request, Rank $rank)
    {
        $formEdit = $this->createForm(RankType::class, $rank);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.rank_manager');
            $rm->updateRank($rank);
            $image = $formEdit->get('image')->getData();
            if ($image instanceof UploadedFile) {
                $rm->updateImage($rank, $image);
            }

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('rank_edit.success.updated', array(), 'param'));

            // Redirect
            if ($request->request->has('update_close')) {
                return $this->redirectToRoute('app_param_rank');
            } else {
                return $this->redirectToRoute('app_param_rank_edit', array('rank' => $rank->getId()));
            }

        }

        if ($request->query->get('delete') == 'image') {

            // Delete image
            $rm = $this->get('app.rank_manager');
            $rm->deleteImage($rank);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('edit.success.image_deleted', array(), 'param'));

            // Redirect
            return $this->redirectToRoute('app_param_rank_edit', array('rank' => $rank->getId()));

        }

        // Render
        return $this->render(
            'param/rank/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'rank' => $rank,
            )
        );
    }

    /**
     * @param Rank $rank
     * @return BinaryFileResponse|Response
     *
     * @Route("/param/rank/image/{rank}",
     *        name="app_param_rank_image",
     *        methods={"GET"},
     *        requirements={"rank"="\d+"})
     */
    public function imageAction(Rank $rank)
    {
        // Member manager
        $rm = $this->get('app.rank_manager');

        // Image
        $image = $rm->getImage($rank);

        if (!is_null($image)) {
            return new BinaryFileResponse($image);
        }

        return new Response('', 404);
    }

    /**
     * @return Response
     *
     * @Route("/param/rank",
     *        name="app_param_rank",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Ranks
        $ranks = $em->getRepository('AppBundle:Rank')->findBy(array(), array('position' => 'ASC', 'name' => 'ASC'));

        // Render
        return $this->render(
            'param/rank/index.html.twig',
            array(
                'ranks' => $ranks,
            )
        );
    }

    /**
     * @param string $ranks
     * @return Response
     *
     * @Route("/param/rank/sort/{ranks}",
     *        name="app_param_rank_sort",
     *        methods={"PUT"},
     *        options={"expose"="true"})
     */
    public function sortAction($ranks)
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        $n = 0;
        foreach (explode(',', $ranks) as $r) {
            $em->getRepository('AppBundle:Rank')->find($r)->setPosition($n++);
        }
        $em->flush();

        return new Response();
    }
}
