<?php

namespace Drupal\lightbox\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Modal edit forms.
 *
 * @ingroup modal
 */
class LightboxForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\lightbox\Entity\Modal */
    $form = parent::buildForm($form, $form_state);
    $validators= [
      'file_validate_extensions' => ['jpg', 'png', 'gif', 'jpeg']
    ];

    $form['background_img'] = [
      '#type' => 'managed_file',
      '#name' => 'background_img',
      '#title' => t('Background Image'),
      '#size' => 50,
      '#description' => t('Background Image'),
      '#validators' => $validators,
      '#upload_location' => 'public://lightbox_background/'
    ];

    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      ];
    }

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;
    $entity->set('fid', $form_state->getValue('background_img')[0]);

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Modal.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Modal.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.modal.canonical', ['modal' => $entity->id()]);
  }

}
