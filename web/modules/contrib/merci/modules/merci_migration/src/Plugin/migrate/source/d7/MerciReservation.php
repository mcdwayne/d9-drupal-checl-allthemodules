<?php

namespace Drupal\merci_migration\Plugin\migrate\source\d7;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Drupal 7 node source from database.
 *
 * @MigrateSource(
 *   id = "merci_reservation",
 *   source_module = "node"
 * )
 */
class MerciReservation extends FieldableEntity {
  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;


/**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityManagerInterface $entity_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_manager);
    $this->moduleHandler = $module_handler;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static($configuration, $plugin_id, $plugin_definition, $migration, $container->get('state'), $container->get('entity.manager'), $container->get('module_handler'));
  }
  

  /**
   * The join options between the node and the node_revisions table.
   */
  const JOIN = 'n.vid = nr.vid';


  
  /**
   * {@inheritdoc}
   */
  public function query() {
    
    // Select node in its last revision.
    $query = $this->select('node_revision', 'nr')->fields('n', [
      'nid',
      'type',
      'language',
      'status',
      'created',
      'changed',
      'comment',
      'promote',
      'sticky',
      'tnid',
      'translate',
    ])->fields('nr', [
      'vid',
      'title',
      'log',
      'timestamp',
    ]);
    $query->addField('n', 'uid', 'node_uid');
    $query->addField('nr', 'uid', 'revision_uid');
    $query->innerJoin('node', 'n', static::JOIN);
    
    // If the content_translation module is enabled, get the source langcode
    // to fill the content_translation_source field.
    if ($this->moduleHandler->moduleExists('content_translation')) {
      $query->leftJoin('node', 'nt', 'n.tnid = nt.nid');
      $query->addField('nt', 'language', 'source_langcode');
    }
    $this->handleTranslations($query);
    if (isset($this->configuration['node_type'])) {
      $query->condition('n.type', $this->configuration['node_type']);
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Get Field API field values.
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');
    foreach (array_keys($this->getFields('node', $row->getSourceProperty('type'))) as $field) {
      $row->setSourceProperty($field, $this->getFieldValues('node', $field, $nid, $vid));
    }

    // Make sure we always have a translation set.
    if ($row->getSourceProperty('tnid') == 0) {
      $row->setSourceProperty('tnid', $row->getSourceProperty('nid'));
    }

    $query = $this->select('merci_reservation_detail', 'mrd')->fields('mrd')->condition('mrd.vid', $vid);

    $result = $query
      ->execute()
      ->fetchAll();

    if (!empty($result)) {
      $row->setSourceProperty('merci_reservation_items', $result);
    }

    return parent::prepareRow($row);
  }

/**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'node_uid' => $this->t('Node authored by (uid)'),
      'revision_uid' => $this->t('Revision authored by (uid)'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'promote' => $this->t('Promoted to front page'),
      'sticky' => $this->t('Sticky at top of lists'),
      'revision' => $this->t('Create new revision'),
      'language' => $this->t('Language (fr, en, ...)'),
      'tnid' => $this->t('The translation set id for this node'),
      'timestamp' => $this->t('The timestamp the latest revision of this node was created.'),
    ];
    return $fields;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
    return $ids;
  }
  
  /**
   * Adapt our query for translations.
   *
   * @param \Drupal\Core\Database\Query\SelectInterface $query
   *   The generated query.
   */
  protected function handleTranslations(SelectInterface $query) {
    
    // Check whether or not we want translations.
    if (empty($this->configuration['translations'])) {
      
      // No translations: Yield untranslated nodes, or default translations.
      $query->where('n.tnid = 0 OR n.tnid = n.nid');
    }
    else {
      
      // Translations: Yield only non-default translations.
      $query->where('n.tnid <> 0 AND n.tnid <> n.nid');
    }
  }


}
