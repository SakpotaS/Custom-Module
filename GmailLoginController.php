<?php

namespace Drupal\email_via_gmail\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\email_via_gmail\Service\GmailProviderService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use League\OAuth2\Client\Provider\Google;

/**
 * Class LoginController
 */
class GmailLoginController extends ControllerBase {

    /**
     * The request stack used to access request globals
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * The Gmail provider service
     *
     * @var \Drupal\email_via_gmail\Service\GmailProviderService
     */
    protected $gmailProvider;

    /**
     * The current session
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function __construct(RequestStack           $request_stack,
                                GmailProviderService   $gmail_provider,) {

        $this->requestStack  = $request_stack;
        $this->gmailProvider = $gmail_provider;

        $this->session = $this->requestStack->getCurrentRequest()->getSession();
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {

        return new static(
            $container->get('request_stack'),
            $container->get('email_via_gmail.gmail_provider'),
        );
    }

    /**
     * Login
     *
     * @return \Drupal\Core\Routing\TrustedRedirectResponse
     *   Redirect to Google authorization login page
     */
    public function login() {

        $provider = $this->gmailProvider->getProvider();

        // set up redirect to Google authorization login page
        $authUrl = $provider->getAuthorizationUrl($this->gmailProvider->getOptions());
        $state   = $provider->getState();
        $this->session->set('oauth2state', $state);

        // may need to add accounts.google.com to your settings.php file:  $settings['trusted_host_patterns'] = ['^accounts\.google\.com$'];
        return new TrustedRedirectResponse($authUrl);
    }
}
