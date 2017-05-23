<?php
namespace MarketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use FOS\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseController
{
    public function loginAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user instanceof User) {
            return new RedirectResponse(
                $this->generateUrl('home')
            );
        }

        return parent::loginAction($request);
    }
}