<?php
namespace AppBundle\Controller;

use AppBundle\Form\ParamType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParamController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/param",
     *        name="app_param_index")
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
}