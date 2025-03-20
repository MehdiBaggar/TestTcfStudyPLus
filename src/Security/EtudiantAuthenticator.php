<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse; // Import JsonResponse
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import the password hasher

class EtudiantAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher // Inject the password hasher
    ) {
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');
        error_log("Attempting authentication for email: " . $email); // LOG

        if (empty($email)) {
            error_log("Email is empty. Throwing CustomUserMessageAuthenticationException."); // LOG
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        $password = $request->request->get('_password');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        try {
            $user = $this->entityManager->getRepository(\App\Entity\Etudiant::class)->findOneBy(['email' => $email]);

            if (!$user) {
                error_log("User not found with email: " . $email . ". Throwing CustomUserMessageAuthenticationException."); // LOG
                throw new CustomUserMessageAuthenticationException('Invalid credentials.');
            }

            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                error_log("Invalid password for user: " . $email . ". Throwing CustomUserMessageAuthenticationException."); // LOG
                throw new CustomUserMessageAuthenticationException('Invalid credentials.');
            }

            error_log("Authentication successful for user: " . $email); // LOG

            return new Passport(
                new UserBadge($email),
                new PasswordCredentials($password),
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                    new RememberMeBadge(),
                ]
            );
        } catch (AuthenticationException $e) {
            error_log("Authentication failed with exception: " . $e->getMessage()); //LOG
            throw $e;  // Re-throw the exception to ensure the framework handles it correctly
        } catch (\Exception $e) {
            error_log("An unexpected error occurred during authentication: " . $e->getMessage());
            throw new AuthenticationException('An unexpected error occurred during authentication.');
        }

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        // Redirect to the admin area by default
        return new RedirectResponse($this->urlGenerator->generate('adminPage'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $errorMessage = $exception->getMessage(); // Get the exception message

        $data = [
            'error' => true,
            'message' => $errorMessage,
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED); // Return a JsonResponse with a 401 status code
    }
}
