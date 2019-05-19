<?php

namespace Drupal\synonyms\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\synonyms\SynonymsService\BehaviorService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative for entity_reference synonyms provider plugin.
 */
class EntityReferenceField extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var BehaviorService
   */
  protected $behaviorService;

  /**
   * Entity type bundle info.
   *
   * @var EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * @var EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  function __construct(EntityTypeManagerInterface $entity_type_manager, BehaviorService $behavior_service, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->behaviorService = $behavior_service;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('synonyms.behaviors'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $implemented_interfaces = new \ReflectionClass($base_plugin_definition['class']);
    $implemented_interfaces = $implemented_interfaces->getInterfaceNames();

    foreach ($this->behaviorService->getBehaviorServices() as $service_id => $behavior) {
      $required_interfaces = $behavior['required_interfaces'];

      $diff = array_diff($required_interfaces, $implemented_interfaces);
      if (empty($diff)) {
        // This plugin has implemented all required interfaces for this
        // behavior.
        foreach ($this->entityTypeManager->getDefinitions() as $entity_type) {
          if ($entity_type instanceof ContentEntityType) {
            foreach ($this->entityTypeBundleInfo->getBundleInfo($entity_type->id()) as $bundle => $bundle_info) {
              $fields = $this->entityFieldManager->getFieldDefinitions($entity_type->id(), $bundle);

              foreach ($fields as $field) {
                if ($field->getType() == 'entity_reference') {
                  $derivative_name = implode('_', [
                    $service_id,
                    $entity_type->id(),
                    $bundle,
                    $field->getName(),
                  ]);

                  $this->derivatives[$derivative_name] = $base_plugin_definition;
                  $this->derivatives[$derivative_name]['label'] = $this->t('@behavior on @field', [
                    '@behavior' => $behavior['service']->getTitle(),
                    '@field' => $field->getLabel(),
                  ]);
                  $this->derivatives[$derivative_name]['synonyms_behavior_service'] = $service_id;
                  $this->derivatives[$derivative_name]['controlled_entity_type'] = $entity_type->id();
                  $this->derivatives[$derivative_name]['controlled_bundle'] = $bundle;
                  $this->derivatives[$derivative_name]['field'] = $field->getName();
                }
              }
            }
          }
        }
      }
    }

    return $this->derivatives;
  }

}
