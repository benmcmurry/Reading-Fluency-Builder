# Google Authentication Setup

This app now authenticates users with Google OAuth 2.0.

## 1) Create OAuth credentials in Google Cloud

1. Open Google Cloud Console: https://console.cloud.google.com/
2. Go to **APIs & Services > Credentials**.
3. Create an **OAuth 2.0 Client ID** for a **Web application**.
4. Add your app callback URL to **Authorized redirect URIs**.

Use this callback URI format:

- `https://YOUR_DOMAIN/PATH_TO_APP/index.php`

Example:

- `https://example.edu/fluencybuilder/index.php`

## 2) Configure environment variables

Set these values in your web server/PHP environment:

- `GOOGLE_CLIENT_ID` (required)
- `GOOGLE_CLIENT_SECRET` (required)
- `GOOGLE_REDIRECT_URI` (optional, defaults to `.../index.php`)
- `GOOGLE_HOSTED_DOMAIN` (optional, e.g. `byu.edu`, to restrict sign-ins)

## 3) Restart PHP/web server

After setting env vars, restart PHP-FPM/Apache/Nginx as needed so PHP can read the new environment values.

## Notes

- Existing session keys remain unchanged (`netid`, `name`, `emailAddress`, etc.).
- For `@byu.edu` accounts, `netid` is derived from the email prefix.
- For other domains, `netid` uses Google `sub` (stable unique user ID).
