# Flarum-saml

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/askvortsov/flarum-saml.svg)](https://packagist.org/packages/askvortsov/flarum-saml)

A [Flarum](http://flarum.org) extension. SAML2 SSO for Flarum

### Installation

Use [Bazaar](https://discuss.flarum.org/d/5151-flagrow-bazaar-the-extension-marketplace) or install manually with composer:

```sh
composer require askvortsov/flarum-saml
```

### Updating

```sh
composer update askvortsov/flarum-saml
```

### TODO:

- Add option for redirect or post binding. Right now this is set to REDIRECT, because POST doesn't work because CSRF exemption isn't yet available in Flarum.

### Links

- [Packagist](https://packagist.org/packages/askvortsov/flarum-saml)
