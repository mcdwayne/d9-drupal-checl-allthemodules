<?php

namespace Drupal\Tests\textimage\Functional;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\Tests\image\Kernel\ImageFieldCreationTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Test Textimage formatters on node display.
 *
 * @group Textimage
 */
class TextimageFieldFormatterTest extends TextimageTestBase {

  use ImageFieldCreationTrait;
  use TestFileCreationTrait;

  /**
   * Set headers to be displayed.
   *
   * @var bool
   */
  protected $dumpHeaders = TRUE;

  /**
   * Test Textimage formatter on node display and text field.
   */
  public function testTextimageTextFieldFormatter() {

    // Create a text field for Textimage test.
    $field_name = strtolower($this->randomMachineName());
    $this->createTextField($field_name, 'article');

    // Create a new node.
    $field_value = '<p>Para1</p><!-- Comment --> Para2  &quot;Title&quot; One &hellip;';
    $nid = $this->createTextimageNode('text', $field_name, $field_value, 'article', 'Overly test');
    $node = Node::load($nid);

    // Get Textimage URL.
    $textimage = $this->textimageFactory->get()
      ->setStyle(ImageStyle::load('textimage_test'))
      ->setTokenData(['node' => $node])
      ->process($field_value);
    $textimage_url = $textimage->getUrl()->toString();
    $rel_url = file_url_transform_relative($textimage_url);

    // Assert HTML tags are stripped and entities are decoded.
    $this->assertSame(['Para1 Para2  "Title" One …'], $textimage->getText());

    // Test the textimage formatter - no link.
    $display = $this->entityDisplayRepository->getViewDisplay('node', $node->getType(), 'default');
    $display_options['type'] = 'textimage_text_field_formatter';
    $display_options['settings']['image_style'] = 'textimage_test';
    $display_options['settings']['image_link'] = '';
    $display_options['settings']['image_alt'] = 'Alternate text: [node:title]';
    $display_options['settings']['image_title'] = 'Title: [node:title]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet('node/' . $nid);
    $elements = $this->cssSelect("img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Unlinked Textimage displaying on full node view.');
    $this->assertSame('Alternate text: Overly test', $elements[0]->getAttribute('alt'));
    $this->assertSame('Title: Overly test', $elements[0]->getAttribute('title'));

    // Test the textimage formatter - linked to content.
    $display_options['settings']['image_link'] = 'content';
    $display->setComponent($field_name, $display_options)
      ->save();
    $href = $node->toUrl()->toString();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href*='$href'] img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Textimage linked to content displaying on full node view.');
    $this->assertSame('Alternate text: Overly test', $elements[0]->getAttribute('alt'));
    $this->assertSame('Title: Overly test', $elements[0]->getAttribute('title'));

    // Test the textimage formatter - linked to Textimage file.
    $display_options['settings']['image_link'] = 'file';
    $display_options['settings']['image_alt'] = 'Alternate text: [node:author]';
    $display_options['settings']['image_title'] = 'Title: [node:author]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href='$textimage_url'] img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Textimage linked to image file displaying on full node view.');
    $this->assertSame($elements[0]->getAttribute('alt'), 'Alternate text: ' . $this->adminUser->getAccountName());
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $this->adminUser->getAccountName());

    // Check that alternate text and title tokens are resolved and their
    // cacheability metadata added.
    $site_name = \Drupal::configFactory()->get('system.site')->get('name');
    $display_options['settings']['image_alt'] = 'Alternate text: [node:author] [site:name]';
    $display_options['settings']['image_title'] = 'Title: [node:author] [site:name]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href='$textimage_url'] img[src='$rel_url']");
    $this->assertSame($elements[0]->getAttribute('alt'), 'Alternate text: ' . $this->adminUser->getAccountName() . ' ' . $site_name);
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $this->adminUser->getAccountName() . ' ' . $site_name);
    $this->assertCacheTag('config:image.style.textimage_test');
    $this->assertCacheTag('config:system.site');

    // Check URI token.
    $bubbleable_metadata = new BubbleableMetadata();
    $token_resolved = \Drupal::service('token')->replace('[textimage:uri:' . $field_name . '] [site:name]', ['node' => $node], [], $bubbleable_metadata);
    $this->assertSame($this->getTextimageUriFromStyleAndText('textimage_test', $field_value) . ' ' . $site_name, $token_resolved);
    $expected_tags = [
      'config:image.style.textimage_test',
      'config:system.site',
      'node:' . $node->id(),
    ];
    $this->assertSame($expected_tags, array_intersect($expected_tags, $bubbleable_metadata->getCacheTags()), 'Token replace produced expected cache tags.');

    // Check URL token.
    $bubbleable_metadata = new BubbleableMetadata();
    $token_resolved = \Drupal::service('token')->replace('[textimage:url:' . $field_name . ']', ['node' => $node], [], $bubbleable_metadata);
    $this->assertSame($this->getTextimageUrlFromStyleAndText('textimage_test', $field_value)->toString(), $token_resolved);
  }

  /**
   * Test Textimage formatter on multi-value text fields.
   */
  public function testTextimageMultiValueTextFieldFormatter() {

    // Create a multi-value text field for Textimage test.
    $field_name = strtolower($this->randomMachineName());
    $this->createTextField($field_name, 'article', ['cardinality' => 4]);

    // Create a new node, with 4 text values for the field.
    $field_value = [];
    for ($i = 0; $i < 4; $i++) {
      $field_value[] = $this->randomMachineName(20);
    }
    $nid = $this->createTextimageNode('text', $field_name, $field_value, 'article', 'Test Title');
    $node = Node::load($nid);

    // Test the textimage formatter - one image.
    $textimage_url = $this->textimageFactory->get()
      ->setStyle(ImageStyle::load('textimage_test'))
      ->setTokenData(['node' => $node])
      ->process($field_value)
      ->getUrl()->toString();
    $rel_url = file_url_transform_relative($textimage_url);

    $display = $this->entityDisplayRepository->getViewDisplay('node', $node->getType(), 'default');
    $display_options['type'] = 'textimage_text_field_formatter';
    $display_options['settings']['image_style'] = 'textimage_test';
    $display_options['settings']['image_text_values'] = 'merge';
    $display_options['settings']['image_alt'] = 'Alternate text: [node:title]';
    $display_options['settings']['image_title'] = 'Title: [node:title]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet('node/' . $nid);
    $elements = $this->cssSelect("div.field--name-{$field_name} div.field__items img");
    $this->assertCount(1, $elements);
    $this->assertSame($rel_url, $elements[0]->getAttribute('src'));
    $this->assertSame('Alternate text: Test Title', $elements[0]->getAttribute('alt'));
    $this->assertSame('Title: Test Title', $elements[0]->getAttribute('title'));

    // Test the textimage formatter - multiple images.
    $display = $this->entityDisplayRepository->getViewDisplay('node', $node->getType(), 'default');
    $display_options['settings']['image_text_values'] = 'itemize';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet('node/' . $nid);
    $elements = $this->cssSelect("div.field--name-{$field_name} div.field__items img");
    $this->assertCount(4, $elements);
    for ($i = 0; $i < 4; $i++) {
      $textimage_url = $this->textimageFactory->get()
        ->setStyle(ImageStyle::load('textimage_test'))
        ->setTokenData(['node' => $node])
        ->process($field_value[$i])
        ->getUrl()->toString();
      $rel_url = file_url_transform_relative($textimage_url);

      $this->assertSame($rel_url, $elements[$i]->getAttribute('src'));
      $this->assertSame('Alternate text: Test Title', $elements[$i]->getAttribute('alt'));
      $this->assertSame('Title: Test Title', $elements[$i]->getAttribute('title'));
    }
  }

  /**
   * Test Textimage formatter on image fields.
   */
  public function testTextimageImageFieldFormatter() {

    // Create an image field for testing.
    $field_name = strtolower($this->randomMachineName());
    $min_resolution = 50;
    $max_resolution = 100;
    $field_settings = [
      'max_resolution' => $max_resolution . 'x' . $max_resolution,
      'min_resolution' => $min_resolution . 'x' . $min_resolution,
      'alt_field' => 1,
    ];
    $this->createImageField($field_name, 'article', [], $field_settings);

    // Create a new node.
    // Get image 'image-1.png'.
    $field_value = $this->getTestFiles('image', 39325)[0];
    $nid = $this->createTextimageNode('image', $field_name, $field_value, 'article', $this->randomMachineName());
    $node = Node::load($nid);
    $node_title = $node->get('title')[0]->get('value')->getValue();

    // Get the stored image.
    $fid = $node->{$field_name}[0]->get('target_id')->getValue();
    $source_image_file = File::load($fid);
    $source_image_file_url = file_create_url($source_image_file->getFileUri());

    // Get Textimage URL.
    $textimage_url = $this->textimageFactory->get()
      ->setSourceImageFile($source_image_file)
      ->setStyle(ImageStyle::load('textimage_test'))
      ->setTokenData(['node' => $node, 'file' => $source_image_file])
      ->process(NULL)
      ->getUrl()->toString();
    $rel_url = file_url_transform_relative($textimage_url);

    // Test the textimage formatter - no link.
    $display = $this->entityDisplayRepository->getViewDisplay('node', $node->getType(), 'default');
    $display_options['type'] = 'textimage_image_field_formatter';
    $display_options['settings']['image_style'] = 'textimage_test';
    $display_options['settings']['image_link'] = '';
    $display_options['settings']['image_alt'] = 'Alternate text: [node:title]';
    $display_options['settings']['image_title'] = 'Title: [node:title]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet('node/' . $nid);
    $elements = $this->cssSelect("img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Unlinked Textimage displaying on full node view.');
    $this->assertSame($elements[0]->getAttribute('alt'), 'Alternate text: ' . $node_title);
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $node_title);

    // Test the textimage formatter - linked to content. Also not providing
    // alt text on formatter leads to rendering the ImageItem alt text.
    $display_options['settings']['image_link'] = 'content';
    $display_options['settings']['image_alt'] = '';
    $display->setComponent($field_name, $display_options)
      ->save();
    $href = $node->toUrl()->toString();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href*='$href'] img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Textimage linked to content displaying on full node view.');
    $this->assertSame($elements[0]->getAttribute('alt'), 'test alt text');
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $node_title);

    // Test the textimage formatter - linked to original image.
    $display_options['settings']['image_link'] = 'file';
    $display_options['settings']['image_alt'] = 'Alternate text: [node:author]';
    $display_options['settings']['image_title'] = 'Title: [node:author]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href='$source_image_file_url'] img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Textimage linked to original image file.');
    $this->assertSame($elements[0]->getAttribute('alt'), 'Alternate text: ' . $this->adminUser->getAccountName());
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $this->adminUser->getAccountName());

    // Test the textimage formatter - linked to derivative image.
    $display_options['settings']['image_link'] = 'derivative';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet($node->toUrl());
    $elements = $this->cssSelect("a[href='$textimage_url'] img[src='$rel_url']");
    $this->assertNotEmpty($elements, 'Textimage linked to derivative image file.');
    $this->assertSame($elements[0]->getAttribute('alt'), 'Alternate text: ' . $this->adminUser->getAccountName());
    $this->assertSame($elements[0]->getAttribute('title'), 'Title: ' . $this->adminUser->getAccountName());

    // Check that alternate text and title tokens are resolved and their
    // cacheability metadata added.
    $site_name = \Drupal::configFactory()->get('system.site')->get('name');
    $display_options['settings']['image_alt'] = 'Alternate text: [node:author] [site:name]';
    $display_options['settings']['image_title'] = 'Title: [node:author] [site:name]';
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet($node->toUrl());
    $this->assertCacheTag('config:image.style.textimage_test');
    $this->assertCacheTag('config:system.site');
    $this->assertCacheTag('node:' . $node->id());
    $this->assertCacheTag('file:' . $source_image_file->id());
    $this->assertCacheTag('user:' . $this->adminUser->id());

    // Check URI token.
    $bubbleable_metadata = new BubbleableMetadata();
    $token_resolved = \Drupal::service('token')->replace('[textimage:uri:' . $field_name . '] [site:name]', ['node' => $node], [], $bubbleable_metadata);
    $textimage = $this->textimageFactory->get()
      ->setSourceImageFile($source_image_file)
      ->setStyle(ImageStyle::load('textimage_test'))
      ->setTokenData(['node' => $node, 'file' => $source_image_file])
      ->process(NULL);
    $this->assertSame($textimage->getUri() . ' ' . $site_name, $token_resolved);
    $expected_tags = [
      'config:image.style.textimage_test',
      'config:system.site',
      'node:' . $node->id(),
      'file:' . $source_image_file->id(),
    ];
    $this->assertEquals($expected_tags, array_intersect($expected_tags, $bubbleable_metadata->getCacheTags()), 'Token replace produced expected cache tags.');

    // Check URL token.
    $bubbleable_metadata = new BubbleableMetadata();
    $token_resolved = \Drupal::service('token')->replace('[textimage:url:' . $field_name . ']', ['node' => $node], [], $bubbleable_metadata);
    $this->assertSame($textimage->getUrl()->toString(), $token_resolved);
  }

  /**
   * Test Textimage caching.
   */
  public function testTextimageCaching() {
    // Create a text field for Textimage test.
    $field_name = 'test_caching';
    $this->createTextField($field_name, 'article');

    // Create a new node.
    $field_value = 'test for caching';
    $nid = $this->createTextimageNode('text', $field_name, $field_value, 'article', 'test');
    $node = Node::load($nid);

    // Set textimage formatter - no link.
    $display = $this->entityDisplayRepository->getViewDisplay('node', $node->getType(), 'default');
    $display_options['type'] = 'textimage_text_field_formatter';
    $display_options['settings']['image_style'] = 'textimage_test';
    $display_options['settings']['image_link'] = '';
    $display_options['settings']['image_build_deferred'] = FALSE;
    $display->setComponent($field_name, $display_options)
      ->save();
    $this->drupalGet('node/' . $nid);

    // From previous get, Textimage was built.
    $this->assertText('Built Textimage');

    // Invalidate the rendered objects cache. Textimage should find the image
    // in its cache.
    Cache::invalidateTags(['rendered']);
    $this->drupalGet('node/' . $nid);
    $this->assertText('Cached Textimage');

    // Invalidate the rendered objects cache, and delete the Textimage cache.
    // Textimage should still find a built image in the store.
    Cache::invalidateTags(['rendered']);
    \Drupal::cache('textimage')->deleteAll();
    $this->drupalGet('node/' . $nid);
    $this->assertText('Stored Textimage');

    // Invalidate 'rendered' again, Textimage should find the image in its
    // cache.
    Cache::invalidateTags(['rendered']);
    $this->drupalGet('node/' . $nid);
    $this->assertText('Cached Textimage');
  }

}
