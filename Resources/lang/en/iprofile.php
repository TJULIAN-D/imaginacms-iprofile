<?php

return [
  'name' => 'Users',
  'notifications' => [
    'titleChangePassword' => 'Set your password to access your account',
    'messageChangePassword' => '<p>To ensure your account is secure, please set your password by following the link below:</p>
    <p style="text-align: center; margin: 20px 0;">
        <a href=":linkPassword" style="display: inline-block; background-color: #007BFF; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">
            Click Here
        </a>
    </p>
    <p>This link will be valid for a limited time. If you encounter any issues or need assistance, please don’t hesitate to contact us.</p>'
  ],
  'welcomeTitleEmail' => "Welcome! We're glad to have you here.",
  'welcomeMessageEmail' => "Hi :userName, \n\nThank you for signing up for :appName. We're excited to have you on board! You can now explore all the features and benefits we offer.",
];
