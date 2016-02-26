<?php
namespace AppBundle\Controller\Param;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Level;
use AppBundle\Form\LevelType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LevelController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/level/add",
     *        name="app_param_level_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        $level = new Level();

        $formEdit = $this->createForm(LevelType::class, $level);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.level_manager');
            $rm->updateLevel($level);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('level_add.success.added', array(), 'param'));

            // Redirect
            return $this->redirectToRoute('app_param_level_edit', array('level' => $level->getId()));

        }

        // Render
        return $this->render(
            'param/level/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Level $level
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/level/edit/{level}",
     *        name="app_param_level_edit",
     *        methods={"GET","POST"},
     *        requirements={"level"="\d+"})
     */
    public function editAction(Request $request, Level $level)
    {
        $formEdit = $this->createForm(LevelType::class, $level);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.level_manager');
            $rm->updateLevel($level);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('level_edit.success.updated', array(), 'param'));

            // Redirect
            if ($request->request->has('update_close')) {
                return $this->redirectToRoute('app_param_level');
            } else {
                return $this->redirectToRoute('app_param_level_edit', array('level' => $level->getId()));
            }

        }

        // Render
        return $this->render(
            'param/level/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
                'level' => $level,
            )
        );
    }

    /**
     * @return Response
     *
     * @Route("/param/level",
     *        name="app_param_level",
     *        methods={"GET","POST"})
     */
    public function indexAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Levels
        $levels = $em->getRepository('AppBundle:Level')->findBy(array(), array('name' => 'ASC'));

        // Render
        return $this->render(
            'param/level/index.html.twig',
            array(
                'levels' => $levels,
            )
        );
    }
}
