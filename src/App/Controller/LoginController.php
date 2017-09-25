<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;

class LoginController {
     
    protected $userProvider;
    protected $paswordEncoder;
    protected $jwtEncoder;

    public function __construct($userProvider, $paswordEncoder, $jwtEncoder)
    {
        $this->userProvider = $userProvider;
        $this->paswordEncoder = $paswordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function __invoke(Request $request)
    {
       $username = $request->get("username");
       $password = $request->get("password");

        try {
            if (empty($username) || empty($password)) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }

            /**
             * @var $user User
             */
            $user = $this->userProvider->loadUserByUsername($username);
    
            if (! $this->paswordEncoder->isPasswordValid($user->getPassword(), $password, '')) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            } else {
                $response = [
                    'success' => true,
                    'token' => $this->jwtEncoder->encode(['name' => $user->getUsername()]),
                ];
            }
        } catch (UsernameNotFoundException $e) {
            $response = [
                'success' => false,
                'error' => 'Invalid credentials',
            ];
        }

        return new JsonResponse($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
    }
}

