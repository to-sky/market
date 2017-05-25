<?php

namespace MarketBundle\EventListener;
 
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
 
class ProfileEditListener implements EventSubscriberInterface
{
    private $oldEmail;
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    private $tokenStorage;
 
    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
    }
 
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_INITIALIZE => 'onProfileEditInitialize',
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess'
        );
    }
 
    public function onProfileEditInitialize(GetResponseUserEvent $event)
    {
        $this->oldEmail = $event->getUser()->getEmail();
    }
    
    public function onProfileEditSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        if ($user->getEmail() !== $this->oldEmail)
        {
            // disable user
            $user->setEnabled(false);
 
            // send confirmation token to new email
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $this->mailer->sendConfirmationEmailMessage($user);
 
            // force user to log-out
            $this->tokenStorage->setToken();
 
            // redirect user to check email page
            $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());
            $url = $this->router->generate('fos_user_registration_check_email');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}