This module is build to seamlessly integrate with the PHP Mailer based SMTP module, enabling authentication for Google Workspace Gmail via OAuth2. It draws inspiration from `phpmailer_oauth2`, originally authored by Ian McLean (imclean). Without Ian's module, I may not have ventured into developing one for Google Gmail.

**Important:** This module relies on the Google `league/oauth2-google` module, which can be found at [here](https://oauth2-client.thephpleague.com/providers/league/). Make sure it's installed in your vendor directory.

Before utilizing this module, follow these steps to set up a Client ID and Client Secret on Google as of April 2024 and configure this module:

1. Sign in to your Google Workspace account.
2. Visit [Google Cloud Console](https://console.cloud.google.com/).
3. Next to "Google Cloud" at the top left, there should be a projects dropdown. Click on it.
4. If you don't have any projects listed, create one and select it as your current project.
5. Click on 'APIs & Services', a prominent button typically found in the center of the page.
6. Navigate to Credentials in the left-hand side menu.
7. Click on CREATE CREDENTIALS, then choose 'OAuth client ID'.
8. Opt for 'Web application' as the Application Type.
9. Name your credentials, and take note of your Client ID and Client Secret. You'll need these values for step (11) below.
10. After installing this module, locate the 'Authorized redirect URI' for your site by navigating to Configuration->System->PHPMailer Gmail OAuth2 (`/admin/config/system/phpmailer-gmail-oauth2`). Open this page in a separate browser window, and you'll find the redirect URI required by Google near the top. Copy the URI and paste it into an 'Authorized redirect URIs' box on the Google page. Save your changes.
11. Back on the Drupal page, enter your Google email address, Client ID, and Client Secret, then click the 'Save configuration' button.
12. Once you've saved your Google credentials, a 'Get Refresh Token' button will appear. Click it to redirect from your site to Google, then follow the instructions provided by Google. Upon completion, Google should issue a Refresh Token and redirect back to the Drupal page you were on. If everything went smoothly, you'll now have your Refresh Token and are ready to send emails.
13. If you encounter issues redirecting to Google when pressing the 'Get Refresh Token' button, you may need to add the following to your settings.php file:

```php
$settings['trusted_host_patterns'] = ['^accounts\.google\.com$'];
```

If you have other `trusted_host_patterns`, append '^accounts\.google\.com$' to the array.

A few additional setup steps:

1. Remember to navigate to the Mail module and select 'PHPMailer SMTP' as your mail Formatter and Sender (`/admin/config/system/mailsystem`).
2. If you have other modules such as Commerce 2 that send mail, you may need to add them at the bottom of the same page mentioned in step (1) above.
3. On the PHPMailer SMTP transport settings page, choose 'Gmail OAuth2' as your SMTP authentication method (`/admin/config/system/phpmailer-smtp`). Set the SMTP port to 587 and select TLS as the secure protocol.

You can utilize the 'Test configuration' service on the PHPMailer SMTP transport settings page to verify that the new configuration is functioning correctly by sending a test email.