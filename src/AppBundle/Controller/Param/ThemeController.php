<?php
namespace AppBundle\Controller\Param;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Theme;
use AppBundle\Form\Type\ThemeDeleteType;
use AppBundle\Form\Type\ThemeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/theme/add",
     *        name="app_param_theme_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        $theme = new Theme();

        $formEdit = $this->createForm(ThemeType::class, $theme);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.theme_manager');
            $rm->updateTheme($theme);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('theme_add.success.added', [], 'param'));

            // Redirect
            return $this->redirectToRoute('app_param_theme_edit', ['theme' => $theme->getId()]);

        }

        // Render
        return $this->render(
            'param/theme/add.html.twig',
            [
                'formEdit' => $formEdit->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Theme $theme
     * @return Response
     *
     * @Route("/param/theme/delete/{theme}",
     *        name="app_param_theme_delete",
     *        methods={"GET","POST"},
     *        requirements={"theme"="\d+"})
     */
    public function deleteAction(Request $request, Theme $theme)
    {
        // Theme manager
        $lm = $this->get('app.theme_manager');

        if (!$lm->isThemeDeletable($theme)) {
            return $this->redirectToRoute('app_param_theme_edit', ['theme' => $theme->getId()]);
        }

        // Delete form
        $formDelete = $this->createForm(ThemeDeleteType::class, $theme);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            // Save data
            $lm->deleteTheme($theme);

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('theme_delete.success.deleted', [], 'param')
            );

            // Redirect
            return $this->redirectToRoute('app_param_theme');

        }

        // Render
        return $this->render(
            'param/theme/delete.html.twig',
            [
                'formDelete' => $formDelete->createView(),
                'theme' => $theme,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Theme $theme
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route("/param/theme/edit/{theme}",
     *        name="app_param_theme_edit",
     *        methods={"GET","POST"},
     *        requirements={"theme"="\d+"})
     */
    public function editAction(Request $request, Theme $theme)
    {
        // Theme manager
        $lm = $this->get('app.theme_manager');

        // Edit form
        $formEdit = $this->createForm(ThemeType::class, $theme);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $rm = $this->get('app.theme_manager');
            $rm->updateTheme($theme);

            // Flash message
            $this->addFlash('success', $this->get('translator')->trans('theme_edit.success.updated', [], 'param'));

            // Redirect
            if ($request->request->has('update_close')) {
                return $this->redirectToRoute('app_param_theme');
            } else {
                return $this->redirectToRoute('app_param_theme_edit', ['theme' => $theme->getId()]);
            }

        }

        // Render
        return $this->render(
            'param/theme/edit.html.twig',
            [
                'deletable' => $lm->isThemeDeletable($theme),
                'formEdit' => $formEdit->createView(),
                'theme' => $theme,
            ]
        );
    }

    /**
     * @return Response
     *
     * @Route("/param/theme",
     *        name="app_param_theme",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Themes
        $themes = $em->getRepository('AppBundle:Theme')->findBy([], ['name' => 'ASC']);

        // Render
        return $this->render(
            'param/theme/index.html.twig',
            [
                'themes' => $themes,
            ]
        );
    }
}
