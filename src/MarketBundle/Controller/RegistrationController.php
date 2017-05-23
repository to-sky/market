<?php

namespace MarketBundle\Controller;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Controller\RegistrationController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user instanceof User) {
            return new RedirectResponse(
                $this->generateUrl('home')
            );
        }

        return parent::registerAction($request);
    }
}
