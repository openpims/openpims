# Cloudflare Turnstile Setup

This document provides instructions on how to set up Cloudflare Turnstile for the registration form in this project.

## What is Cloudflare Turnstile?

Cloudflare Turnstile is a CAPTCHA alternative that helps protect websites from bots and spam. It's designed to be more user-friendly than traditional CAPTCHAs while still providing effective protection.

## Setup Instructions

1. **Create a Cloudflare Account**:
   - If you don't already have one, create an account at [Cloudflare](https://www.cloudflare.com/).

2. **Add Turnstile to Your Cloudflare Account**:
   - Go to the Cloudflare dashboard.
   - Navigate to "Security" > "Turnstile".
   - Click "Add Site" and follow the instructions.

3. **Get Your Site Key and Secret Key**:
   - After adding your site, you'll be provided with a Site Key and a Secret Key.
   - Copy these keys as you'll need them for the next step.

4. **Add the Keys to Your .env File**:
   - Open your project's `.env` file.
   - Add the following lines:
     ```
     TURNSTILE_SITE_KEY=your_site_key_here
     TURNSTILE_SECRET_KEY=your_secret_key_here
     ```
   - Replace `your_site_key_here` and `your_secret_key_here` with the actual keys from Cloudflare.

5. **Clear Configuration Cache**:
   - Run the following command to clear the configuration cache:
     ```
     php artisan config:clear
     ```

6. **Test the Registration Form**:
   - Visit the registration page of your application.
   - You should see the Turnstile widget before the submit button.
   - Try to register with and without completing the Turnstile challenge to ensure it's working correctly.

## Troubleshooting

- If the Turnstile widget doesn't appear, make sure the JavaScript is loaded correctly and the site key is set properly.
- If registration fails even after completing the Turnstile challenge, check the secret key and ensure the verification URL is correct.
- For more detailed troubleshooting, check the Laravel logs.