<?php

namespace App\Security;


use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $utilisateurRepository;
    
    private $passwordEncoder;
    
    public function __construct(UtilisateurRepository $utilisateurRepository, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function supports(Request $request)
    {
        // do your work when we're POSTing to the login page
        return $request->attributes->get('_route') === 'login_form' && $request->isMethod('POST');
    }
    
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request ->get('login')['email'],
            'password' => $request->request ->get('login')['password'],
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );
        return $credentials;
        
    }
    
    public function getUser($credentials, UserProviderInterface $userProvider)
    {  
            //return true;
        return $this->utilisateurRepository->findOneBy(['email' => $credentials['email']]);
    }
    
    public function checkCredentials($credentials, UserInterface $user)
    {
       
        //return true;
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //dd('successfully logged');
        return new RedirectResponse($this->router->generate('dashboard'));
    }
    
    protected function getLoginUrl()
    {
        return $this->router->generate('login_form');
    }
}
