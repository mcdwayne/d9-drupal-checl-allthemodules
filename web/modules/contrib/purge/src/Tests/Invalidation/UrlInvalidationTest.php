<?php

namespace Drupal\purge\Tests\Invalidation;

/**
 * Tests \Drupal\purge\Plugin\Purge\Invalidation\UrlInvalidation.
 *
 * @group purge
 * @see \Drupal\purge\Plugin\Purge\Invalidation\InvalidationInterface
 */
class UrlInvalidationTest extends PluginTestBase {
  protected $pluginId = 'url';
  protected $expressions = [
    'http://www.test.com',
    'https://domain/path',
    'http://domain/path?param=1',
  ];
  protected $expressionsInvalid = [
    NULL,
    '',
    "35423523",
    'http:// /aa',
    'http://www.test.com/*',
  ];

}
