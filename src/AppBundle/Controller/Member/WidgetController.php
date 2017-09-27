<?php

namespace AppBundle\Controller\Member;

use AppBundle\Entity\Membership;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function missingMembershipNumberAction()
    {
        // Results
        $missing = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Membership::class)
            ->findMissingNumbers($this->getUser()->getCurrentSeason());

        // Render
        return $this->render(
            'member/widget/_missing.membership.number.html.twig',
            [
                'missing' => $missing,
            ]
        );
    }
}
