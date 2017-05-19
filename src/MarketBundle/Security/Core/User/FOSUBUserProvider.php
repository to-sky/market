<?php

namespace MarketBundle\Security\Core\User;

use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use MarketBundle\Entity\User;

class FOSUBUserProvider extends BaseFOSUBProvider
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {        
        $property = $this->getProperty($response);
        $facebook_id = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $facebook_id))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($facebook_id);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $facebook_id = $response->getUsername();
        $username = $response->getRealName();
        $user_email = $response->getEmail();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $facebook_id));

        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($facebook_id);
            $user->$setter_token($response->getAccessToken());
            $password = password_hash($username, PASSWORD_BCRYPT);
            $user->setUsername($username);
            $user->setEmail($user_email);
            $user->setPassword($password);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            
            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }
}