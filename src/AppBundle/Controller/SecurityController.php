<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/login",
     *     name="app_security_login",
     *     methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // Login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array('error' => $error, 'lastUserName' => $lastUsername));
    }

    /**
     * @Route("/login/check",
     *     name="app_security_login_check",
     *     methods={"GET"})
     */
    public function loginCheckAction()
    {
        // Never be executed
    }

    /**
     * @Route("/logout",
     *     name="app_security_logout",
     *     methods={"GET"})
     */
    public function logoutAction()
    {
        // Never be executed
    }
}
