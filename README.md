# Flarum SAML2 SSO (flarum-saml)

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/askvortsov/flarum-saml.svg)](https://packagist.org/packages/askvortsov/flarum-saml)

A Flarum extension to support SAML2 SSO Login and Registration on Flarum. This opens up Flarum for use as an internal corporate discussion/community tool.

### Installation

Use [Bazaar](https://discuss.flarum.org/d/5151-flagrow-bazaar-the-extension-marketplace) or install manually with composer:

```sh
composer require askvortsov/flarum-saml
```

### Updating

```sh
composer update askvortsov/flarum-saml
```

### Attribute Sync

Flarum SAML integrates with [Flarum Auth Sync](https://github.com/askvortsov1/flarum-auth-sync), which lets you sync user avatars, bios, groups, and masquerade attributes via SAML Response Attributes. To this feature:

- Enable it in settings
- Make sure that [Flarum Auth Sync](https://github.com/askvortsov1/flarum-auth-sync) is enabled and configured properly
- Make sure that [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio) and [Friends of Flarum Masquerade](https://github.com/FriendsOfFlarum/masquerade) are enabled if you'd like to use those integrations.

Have your SAML Identity Provider include the following in attributes (make sure that keys are lowercase):

- `avatar`: A URL pointing to an image for the user's avatar. Make sure that the file type is compatible with Flarum (jpeg or png I believe).
- `group`: A comma-separated list of ids for groups that a user should belong to. Keep in mind that this will both add and remove groups, so make sure that all desired groups are included.
- `bio`: A string that will be synced to the user's bio if [Friends of Flarum User Bios](https://github.com/FriendsOfFlarum/user-bio) is enabled
- For any masquerade attributes you want to sync, make sure that the SAML attribute key matches the name of the profile field.

If one of these isn't included, or doesn't work, the rest should still work.

### TODO

- Replace CSRF exemption workaround when Flarum Beta 12 is released.
- Add support for using getting Identity Provider Metadata via a url vs having to paste it.
- Add support for signing/encrypting SAMLRequests.

### Feedback

Super excited to be posting my first extensions, hopefully more to follow! If you run into issues or have feature requests, let me know and I'll look into it!

### Links

- [Github](https://github.com/askvortsov1/flarum-saml)
- [Flagrow](https://flagrow.io/extensions/askvortsov/flarum-saml)
- [Packagist](https://packagist.org/packages/askvortsov/flarum-saml)
- [Discuss](https://discuss.flarum.org/d/22757-flarum-saml)
