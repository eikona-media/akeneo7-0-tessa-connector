<p align="center">
  <a href="https://www.tessa-dam.com/" target="_blank" rel="noopener noreferrer">
    <img src="tessa-logo.svg" width=250 alt="TESSA Logo"/>
  </a>
</p>

<p>&nbsp;</p>

<h1 align="center">
  TESSA Connector for Akeneo 7.0
</h1>

<p>&nbsp;</p>

With this Connector Bundle you seamlessly connect Akeneo with the Digital Asset Management solution "TESSA" (https://www.tessa-dam.com).
This provides you with a professional and fully integrated DAM solution for Akeneo to centrally store,
manage and use all additional files for your products (e.g. images, videos, documents, etc.) in all channels.

More informationen is available at our [website](https://www.tessa-dam.com/).

## Requirements

| Akeneo                        | Version |
|:-----------------------------:|:-------:|
| Akeneo PIM Community Edition  | ~7.0.0  |
| Akeneo PIM Enterprise Edition | ~7.0.0  |

## Installation

1) Install the bundle with composer
```bash
composer require eikona-media/akeneo7-0-tessa-connector
```

2) Then add the following lines **at the end** of your config/routes/routes.yml :
```yaml
tessa_media:
    resource: "@EikonaTessaConnectorBundle/Resources/config/routing.yml"
```

3) Enable the bundle in the `config/bundles.php` file:
```php
return [
    // ...
    Eikona\Tessa\ConnectorBundle\EikonaTessaConnectorBundle::class => ['all' => true],
];

```

4) EE Only: Enable the Enterprise bundle in the `config/bundles.php` file:
```php
return [
    // ...
    Eikona\Tessa\EEConnectorBundle\EikonaTessaEEConnectorBundle::class => ['all' => true],
];
```

5) Run the following commands in your project root:
```bash
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
php bin/console pim:installer:dump-require-paths --env=prod
php bin/console pim:installer:assets --env=prod
yarn run webpack
yarn run less
yarn run update-extensions
```

6) Configure the Tessa Connector in the Akeneo UI

## How to use with reference entities

1) Add the following lines **at the end** of your config/routes/routes.yml :
```yaml
tessa_api_reference_data:
  resource: "@EikonaTessaReferenceDataAttributeBundle/Resources/config/routing.yml"
```

2) Enable the ReferenceDataAttributeBundle in the `config/bundles.php` file:
```php
return [
    // ...
    Eikona\Tessa\ReferenceDataAttributeBundle\EikonaTessaReferenceDataAttributeBundle::class => ['all' => true], // New
];
```

3) Select TESSA in the type dropdown when you add a new reference entity attribute
