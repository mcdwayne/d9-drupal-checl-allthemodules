<?php

/**
 * @file
 * Contains \Drupal\zsm_haproxy\Form\ZSMHAProxyDeleteForm.
 */

namespace Drupal\zsm_haproxy\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a content_entity_example entity.
 *
 * @ingroup zsm_haproxy
 */
class ZSMHAProxyPluginDeleteForm extends ContentEntityConfirmFormBase {

    /**
     * {@inheritdoc}
     */
    public function getQuestion() {
        return $this->t('Are you sure you want to delete entity %name?', array('%name' => $this->entity->label()));
    }

    /**
     * {@inheritdoc}
     *
     * If the delete command is canceled, return to the contact list.
     */
    public function getCancelUrl() {
        return new Url('entity.zsm_haproxy_plugin.collection');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText() {
        return $this->t('Delete');
    }

    /**
     * {@inheritdoc}
     *
     * Delete the entity and log the event. logger() replaces the watchdog.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $entity = $this->getEntity();
        $entity->delete();

        $this->logger('zsm_haproxy')->notice('deleted %title.',
            array(
                '%title' => $this->entity->label(),
            ));
        // Redirect to zsm_core list after delete.
        $form_state->setRedirect('entity.zsm_haproxy_plugin.collection');
    }

}
