<?php

namespace Drupal\email_via_gmail\Form;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to gather and store Gmail OAuth2 parameters
 */
class GmailOauth2SettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {

        return 'email_via_gmail_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {

        return ['email_via_gmail.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

        $config       = $this->configFactory()->getEditable('email_via_gmail.gmail_settings');
        $provider_url = Url::fromRoute('email_via_gmail.gmail_callback')->setAbsolute()->toString();
        $config->set('gm_provider_url', $provider_url);
        $config->save();

        // get saved config data for display
        $email_address = $config->get('gm_email_address');
        $client_id     = $config->get('gm_client_id');
        $client_secret = $config->get('gm_client_secret');
        $refresh_token = $config->get('gm_refresh_access_token');
        $refresh_token = empty($refresh_token) ? "Empty" : $refresh_token;

        $form['gm_auth'] = [
            '#type'          => 'details',
            '#title'         => $this->t('Gmail OAuth2'),
            '#open'          => True,
        ];

        $form['gm_auth']['info1'] = [
            '#type'          => 'markup',
            '#markup'        => $this->t('The following Redirect URI must be added to Google before attempting to retrieve a Refresh token: <strong>:uri</strong>', [
              ':uri' => $provider_url,
            ]),
        ];

        $form['gm_auth']['info2'] = [
            '#type'          => 'markup',
            '#markup'        => $this->t('<p>To retrieve a Refresh Token, fill out the form below and press the <strong>Save configuration</strong>. '              .
                                         'button. A <strong>Get Refresh Token</strong> button will appear after the configuration data has been saved.</p><p>  '    .
                                         'Press the <strong>Get Refresh Token</strong> button to be redirected to '                                                 .
                                         'Google, where you will sign in and follow the instructions. Assuming everything goes smoothly, you should eventually '    .
                                         'be redirected back to this page, where the <strong>Refresh Token</strong> will display in a message at the top of the  '  .
                                         'page.</p><p>There is no need to remember the token; it is only displayed to let you know it was received. Once you have ' .
                                         'the Token, your setup is complete and email should work. Complete the setup on the Mail System and PHPMailer SMTP pages'),
        ];

    
        $form['gm_auth']['info3'] = [
            '#type'          => 'markup',
            '#markup'        => $this->t('<p>The current Refresh Token is :token', [':token' => $refresh_token]),
        ];

        $form['gm_auth']['gm_email_address'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Email address'),
            '#default_value' => $email_address,
        ];

        $form['gm_auth']['gm_client_id'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Client ID'),
            '#default_value' => $client_id,
        ];

        $form['gm_auth']['gm_client_secret'] = [
        #   '#type'          => 'textfield',
            '#type'          => 'password',
            '#title'         => $this->t('Client secret'),
            '#default_value' => $client_secret,
            '#description'   => $this->t('Leave empty to use the current secret'),
        ];

        if($email_address && $client_id && $client_secret) {
            $form['gm_auth']['gm_login'] = [
                '#title'         => $this->t('Get Refresh Token'),
                '#type'          => 'link',
                '#url'           => Url::fromRoute('email_via_gmail.gmail_login'),
                '#attributes'    => [
                  'class' => ['button'],
                ],
            ];
        }

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $values = $form_state->getValues();

        // Save the configuration changes.
        $config = $this->configFactory()->getEditable('email_via_gmail.gmail_settings');
        $config->set('gm_email_address', $values['gm_email_address']);
        $config->set('gm_client_id', $values['gm_client_id']);

        // Check if client secret is empty and don't overwrite current one if so.
        if ($values['gm_client_secret'] !== '') {
            $config->set('gm_client_secret', $values['gm_client_secret']);
        }

        $config->save();

        parent::submitForm($form, $form_state);
    }
}
