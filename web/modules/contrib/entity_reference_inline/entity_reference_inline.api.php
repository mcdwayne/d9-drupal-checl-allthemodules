<?php

/**
 * @file
 * Callbacks and hooks related to the entity reference inline form system.
 */

/**
 * Perform alterations before a entity form build through the entity reference
 * inline widget is rendered.
 *
 * In addition to hook_entity_reference_inline_form_alter(), which is called
 * for all entity forms generated by the entity reference inline widget, there
 * are two more specific form hooks available. The first,
 * hook_entity_reference_inline_ENTITY_TYPE_form_alter(), allows targeting of a
 * form/forms for a specific entity type. The second,
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter(), can be used
 * to target a specific form mode for an entity type directly.
 *
 * The call order is as follows: all existing form alter functions are called
 * for module A, then all for module B, etc., followed by all for any base
 * theme(s), and finally for the theme itself. The module order is determined
 * by system weight, then by module name.
 *
 * Within each module, form alter hooks are called in the following order:
 * first, hook_entity_reference_inline_form_alter(); second,
 * hook_entity_reference_inline_ENTITY_TYPE_form_alter(); third,
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter(). So, for
 * each module, the more general hooks are called first followed by the more
 * specific.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - entity: The entity for which the entity form has been generated.
 *   - form_display: The form display that the current sub form operates with.
 *   - parent_item: The field item by which the entity is being referenced.
 *   - wrapped_entity_form: The wrapped entity form as returned by
 *     \Drupal\entity_reference_inline\Plugin\Field\FieldWidget\EntityReferenceInlineWidget::formElement().
 *
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter()
 */
function hook_entity_reference_inline_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Provide a entity type form-specific alteration instead of the global
 * hook_entity_reference_inline_form_alter().
 *
 * Modules can implement
 * hook_entity_reference_inline_ENTITY_TYPE_form_alter() to modify a
 * specific form for a specific entity type, rather than implementing
 * hook_entity_reference_inline_form_alter() and checking the entity type, or
 * using long switch statements to alter multiple forms.
 *
 * Form alter hooks are called in the following order:
 * hook_entity_reference_inline_form_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_form_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter(). See
 * hook_entity_reference_inline_form_alter() for more details.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - entity: The entity for which the entity form has been generated.
 *   - form_display: The form display that the current sub form operates with.
 *   - parent_item: The field item by which the entity is being referenced.
 *   - wrapped_entity_form: The wrapped entity form as returned by
 *     \Drupal\entity_reference_inline\Plugin\Field\FieldWidget\EntityReferenceInlineWidget::formElement().
 *
 * @see hook_entity_reference_inline_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 */
function hook_entity_reference_inline_ENTITY_TYPE_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Provide a entity type and form mode form-specific alteration instead of the
 * global hook_entity_reference_inline_form_alter().
 *
 * Modules can implement
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter() to modify a
 * specific form for a specific entity type and form mode,
 * rather than implementing hook_entity_reference_inline_form_alter() and
 * checking the form ID, or using long switch statements to alter multiple
 * forms.
 *
 * Form alter hooks are called in the following order:
 * hook_entity_reference_inline_form_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_form_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter(). See
 * hook_entity_reference_inline_form_alter() for more details.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - entity: The entity for which the entity form has been generated.
 *   - form_display: The form display that the current sub form operates with.
 *   - parent_item: The field item by which the entity is being referenced.
 *   - wrapped_entity_form: The wrapped entity form as returned by
 *     \Drupal\entity_reference_inline\Plugin\Field\FieldWidget\EntityReferenceInlineWidget::formElement().
 *
 * @see hook_entity_reference_inline_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 */
function hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Perform alterations over all referenced inline items before a entity form build
 * through the entity reference inline widget is rendered.
 *
 * This is in particular useful if need to alter remove and add buttons.
 *
 * In addition to hook_entity_reference_inline_form_multiple_elements_alter(),
 * which is called for all entity forms generated by the entity reference inline
 * widget, there are two more specific form hooks available. The first,
 * hook_entity_reference_inline_ENTITY_TYPE_form_multiple_elements_alter(),
 * allows targeting of a form/forms for a specific entity type. The second,
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_multiple_elements_alter(),
 * can be used to target a specific form mode for an entity type directly.
 *
 * The call order is as follows: all existing form alter functions are called
 * for module A, then all for module B, etc., followed by all for any base
 * theme(s), and finally for the theme itself. The module order is determined
 * by system weight, then by module name.
 *
 * Within each module, form alter hooks are called in the following order:
 * first, hook_entity_reference_inline_form_multiple_elements_alter(); second,
 * hook_entity_reference_inline_ENTITY_TYPE_form_multiple_elements_alter(); third,
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_multiple_elements_alter().
 * So, for each module, the more general hooks are called first followed by the
 * more specific.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - items: The FieldItemList of the entity reference.
 *
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_alter()
 */
function hook_entity_reference_inline_form_multiple_elements_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Provide a entity type form-specific alteration instead of the global
 * hook_entity_reference_inline_form_multiple_elements_alter().
 *
 * Modules can implement
 * hook_entity_reference_inline_ENTITY_TYPE_form_multiple_elements_alter() to
 * modify a specific form for a specific entity type, rather than implementing
 * hook_entity_reference_inline_form_multiple_elements_alter() and checking the
 * entity type, or using long switch statements to alter multiple forms.
 *
 * Form alter hooks are called in the following order:
 * hook_entity_reference_inline_form_multiple_elements_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_form_multiple_elements_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_multiple_elements_alter().
 *
 * See hook_entity_reference_inline_form_multiple_elements_alter() for more details.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - items: The FieldItemList of the entity reference.
 *
 * @see hook_entity_reference_inline_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 */
function hook_entity_reference_inline_ENTITY_TYPE_form_multiple_elements_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Provide a entity type and form mode form-specific alteration instead of the
 * global hook_entity_reference_inline_form_multiple_elements_alter().
 *
 * Modules can implement
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_multiple_elements_alter()
 * to modify a specific form for a specific entity type and form mode,
 * rather than implementing hook_entity_reference_inline_form_multiple_elements_alter()
 * and checking the form ID, or using long switch statements to alter multiple
 * forms.
 *
 * Form alter hooks are called in the following order:
 * hook_entity_reference_inline_form_multiple_elements_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_multiple_elements_form_alter(),
 * hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_multiple_elements_form_alter().
 *
 * See hook_entity_reference_inline_form_multiple_elements_alter() for more details.
 *
 * @param $form
 *   Nested array of form elements that comprise the entity reference inline
 *   form.
 * @param $form_state
 *   The current state of the form. The arguments that
 *   \Drupal::formBuilder()->getForm() was originally called with are available
 *   in the array $form_state->getBuildInfo()['args'].
 * @param $context
 *   An associative array containing:
 *   - items: The FieldItemList of the entity reference.
 *
 * @see hook_entity_reference_inline_form_alter()
 * @see hook_entity_reference_inline_ENTITY_TYPE_form_alter()
 */
function hook_entity_reference_inline_ENTITY_TYPE_FORM_MODE_form_multiple_elements_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, array $context) {}

/**
 * Perform alterations to the build defaults render array before it is given to
 * the renderer for rendering. This hook will be called with the output of
 * \Drupal\Core\Entity\EntityViewBuilder::view(), therefore the build array
 * contains only meta information about how the entity should be rendered.
 *
 * @param array &$build
 *   The render array containing the build defaults for the inline entity.
 * @param \Drupal\Core\Entity\EntityInterface $inline_entity
 *   The inline entity for which the render array is built.
 * @param \Drupal\Core\Field\FieldItemInterface $field_item
 *   The parent entity field item on which the inline entity is located.
 *
 * @see hook_ENTITY_TYPE_inline_view_build_defaults_alter
 */
function hook_entity_inline_view_build_defaults_alter(&$build, \Drupal\Core\Entity\EntityInterface $inline_entity, \Drupal\Core\Field\FieldItemInterface $field_item) {}

/**
 * Provide an entity type specific alteration instead of the global
 * hook_entity_inline_view_build_defaults_alter().
 *
 * Modules can implement
 * hook_ENTITY_TYPE_inline_view_build_defaults_alter() to modify the build
 * defaults render array for a specific inline rendered entity type, rather than
 * implementing hook_entity_inline_view_build_defaults_alter() and checking the
 * entity type of the inline entity.
 *
 * @param array &$build
 *   The render array containing the build defaults for the inline entity.
 * @param \Drupal\Core\Entity\EntityInterface $inline_entity
 *   The inline entity for which the render array is built.
 * @param \Drupal\Core\Field\FieldItemInterface $field_item
 *   The parent entity field item on which the inline entity is located.
 *
 * @see hook_entity_inline_view_build_defaults_alter
 */
function hook_ENTITY_TYPE_inline_view_build_defaults_alter(&$build, \Drupal\Core\Entity\EntityInterface $inline_entity, \Drupal\Core\Field\FieldItemInterface $field_item) {}
