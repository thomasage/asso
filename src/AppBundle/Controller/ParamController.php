<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Rank;
use AppBundle\Form\ParamType;
use AppBundle\Form\RankType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParamController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/param",
     *        name="app_param_index",
     *        methods={"GET","POST"})
     */
    public function indexAction(Request $request)
    {
        $formParam = $this->createForm(ParamType::class);
        $formParam->handleRequest($request);

        if ($formParam->isSubmitted() && $formParam->isValid()) {

            // Update password
            $data = $formParam->getData();
            $user = $this->getUser();
            $encoder = $this->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, $data['password']));
            $this->getDoctrine()->getManager()->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('index.success.password_updated', array(), 'param')
            );

            // Redirect
            return $this->redirectToRoute('app_param_index');

        }

        // Render
        return $this->render(
            'param/index.html.twig',
            array(
                'formParam' => $formParam->createView(),
            )
        );
    }

    /**
     * @return Response
     *
     * @Route("/param/rank",
     *        name="app_param_rank",
     *        methods={"GET","POST"})
     */
    public function rankAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Ranks
        $ranks = $em->getRepository('AppBundle:Rank')->findBy(array(), array('position' => 'ASC', 'name' => 'ASC'));

        // Render
        return $this->render(
            'param/rank.html.twig',
            array(
                'ranks' => $ranks,
            )
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/param/rank/add",
     *        name="app_param_rank_add",
     *        methods={"GET","POST"})
     */
    public function rankAddAction(Request $request)
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
            return $this->redirectToRoute('app_param_rank_edit', array('rank' => $rank->getId()));

        }

        // Render
        return $this->render(
            'param/rank.add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Rank $rank
     * @return Response
     *
     * @Route("/param/rank/{rank}",
     *        name="app_param_rank_edit",
     *        methods={"GET","POST"},
     *        requirements={"rank"="\d+"})
     */
    public function rankEditAction(Request $request, Rank $rank)
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
            'param/rank.edit.html.twig',
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
     *        methods={"GET","POST"},
     *        requirements={"rank"="\d+"})
     */
    public function rankImageAction(Rank $rank)
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
}