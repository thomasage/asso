<?php
namespace AppBundle\Controller\Param;

use AppBundle\Form\ParamType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/param",
     *        name="app_param_index",
     *        methods={"GET"})
     */
    public function indexAction()
    {
        // Redirect
        return $this->redirectToRoute('app_param_season');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/param/password",
     *        name="app_param_password",
     *        methods={"GET","POST"})
     */
    public function passwordAction(Request $request)
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
                $this->get('translator')->trans('password.success.password_updated', array(), 'param')
            );

            // Redirect
            return $this->redirectToRoute('app_param_password');

        }

        // Render
        return $this->render(
            'param/password.html.twig',
            array(
                'formParam' => $formParam->createView(),
            )
        );
    }
}