<?php

namespace Drupal\trail_graph\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Controller\NodePreviewController;

/**
 * Class SimpleNodePreviewController.
 */
class SimpleNodePreviewController extends NodePreviewController {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $node_preview, $view_mode_id = 'full', $langcode = NULL) {
    $node_preview->preview_view_mode = $view_mode_id;
    $build = parent::view($node_preview, $view_mode_id);

    $build['#attached']['library'][] = 'node/drupal.node.preview';

    // Don't render cache previews.
    unset($build['#cache']);

    return $build;
  }

  /**
   * The _title_callback for the page that renders a single node in preview.
   *
   * @param \Drupal\Core\Entity\EntityInterface $node_preview
   *   The current node.
   *
   * @return string
   *   The page title.
   */
  public function title(EntityInterface $node_preview) {
    return $this->entityManager->getTranslationFromContext($node_preview)->label();
  }

}
