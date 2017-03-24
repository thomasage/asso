<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     *
     * @Route("/deploy",
     *     name="app_deploy",
     *     methods={"POST"},
     *     requirements={"_format":"json"})
     */
    public function indexAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->addDebug('HTTP_X_EVENT_KEY='.$request->headers->get('HTTP_X_EVENT_KEY'));

        if (!$request->headers->has('HTTP_X_EVENT_KEY')
            || $request->headers->get('HTTP_X_EVENT_KEY') !== 'repo:push'
        ) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        if (!$request->headers->has('HTTP_X_HOOK_UUID')
            || $request->headers->get('HTTP_X_HOOK_UUID') !== '968e0934-6f12-4050-a3d3-4346b71fa1e8'
        ) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        if (!$request->headers->has('USER_AGENT')
            || preg_match('/^Bitbucket-Webhooks\/\d.\d$/', $request->headers->get('USER_AGENT')) !== 1
        ) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        if (!IpUtils::checkIp4($request->getClientIp(), '104.192.143.0/24')) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        file_put_contents(__DIR__.'/../log.txt', $request->getContent());

//        if ($update) {
//            // Do a git checkout to the web root
//            exec('cd ' . $repo_dir . ' && ' . $git_bin_path  . ' fetch');
//            exec('cd ' . $repo_dir . ' && GIT_WORK_TREE=' . $web_root_dir . ' ' . $git_bin_path  . ' checkout -f');
//
//            // Log the deployment
//            $commit_hash = shell_exec('cd ' . $repo_dir . ' && ' . $git_bin_path  . ' rev-parse --short HEAD');
//            file_put_contents('deploy.log', date('m/d/Y h:i:s a') . " Deployed branch: " .  $branch . " Commit: " . $commit_hash . "\n", FILE_APPEND);
//        }

        return new Response('OK');
    }
}
