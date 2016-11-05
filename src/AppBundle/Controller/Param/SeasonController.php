<?php
namespace AppBundle\Controller\Param;

use AppBundle\Entity\Season;
use AppBundle\Form\SeasonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SeasonController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/param/season/add",
     *        name="app_param_season_add",
     *        methods={"GET","POST"})
     */
    public function addAction(Request $request)
    {
        $season = new Season();
        $formEdit = $this->createForm(SeasonType::class, $season);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $em = $this->getDoctrine()->getManager();
            $em->persist($season);
            $em->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('season_add.success.added', array(), 'param')
            );

            // Redirect
            return $this->redirectToRoute('app_param_season');

        }

        // Render
        return $this->render(
            'param/season/add.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @param Season $season
     * @return Response
     *
     * @Route("/param/season/active/{season}",
     *        name="app_param_season_active",
     *        methods={"PUT"},
     *        requirements={"season"="\d+"},
     *        options={"expose"="true"})
     */
    public function activeAction(Season $season)
    {
        $this->getUser()->setCurrentSeason($season);
        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }

    /**
     * @param Request $request
     * @param Season $season
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/param/season/edit/{season}",
     *        name="app_param_season_edit",
     *        methods={"GET","POST"})
     */
    public function editAction(Request $request, Season $season)
    {
        $formEdit = $this->createForm(SeasonType::class, $season);
        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {

            // Save data
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Flash message
            $this->addFlash(
                'success',
                $this->get('translator')->trans('season_edit.success.updated', array(), 'param')
            );

            // Redirect
            return $this->redirectToRoute('app_param_season');

        }

        // Render
        return $this->render(
            'param/season/edit.html.twig',
            array(
                'formEdit' => $formEdit->createView(),
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/param/season",
     *        name="app_param_season",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Entity manager
        $em = $this->getDoctrine()->getManager();

        // Seasons
        $seasons = $em->getRepository('AppBundle:Season')->findBy(array(), array('start' => 'DESC'));

        // Render
        return $this->render(
            'param/season/index.html.twig',
            array(
                'seasons' => $seasons,
            )
        );
    }
}
