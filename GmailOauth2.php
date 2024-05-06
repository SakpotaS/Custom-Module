<?php

namespace Drupal\email_via_gmail\Plugin\PhpmailerOauth2;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\email_via_gmail\Service\GmailProviderService;
use Drupal\phpmailer_smtp\Plugin\PhpmailerOauth2\PhpmailerOauth2PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Gmail OAuth2 plugin.
 *
 * @PhpmailerOauth2(
 *   id = "gmail",
 *   name = @Translation("Gmail OAuth2"),
 * )
 */
class GmailOauth2 extends PhpmailerOauth2PluginBase implements ContainerFactoryPluginInterface {

    /**
     * The Gmail provider service
     *
     * @var \Drupal\email_via_gmail\Service\GmailProviderService
     */
    protected $gmailProvider;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, GmailProviderService $gmail_provider) {

        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->gmailProvider = $gmail_provider;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('email_via_gmail.gmail_provider')
        );
    }

    /**
     * {@inheritdoc}
     */

    public function getAuthOptions() {

        return $this->gmailProvider->getAuthOptions();
    }
}
