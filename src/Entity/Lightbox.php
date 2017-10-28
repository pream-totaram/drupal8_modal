<?php

namespace Drupal\lightbox\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Lightbox entity.
 *
 * @ingroup lightbox
 *
 * @ContentEntityType(
 *   id = "lightbox",
 *   label = @Translation("Lightbox"),
 *   handlers = {
 *     "storage" = "Drupal\lightbox\LightboxStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lightbox\LightboxListBuilder",
 *     "views_data" = "Drupal\lightbox\Entity\LightboxViewsData",
 *     "translation" = "Drupal\lightbox\LightboxTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\lightbox\Form\LightboxForm",
 *       "add" = "Drupal\lightbox\Form\LightboxForm",
 *       "edit" = "Drupal\lightbox\Form\LightboxForm",
 *       "delete" = "Drupal\lightbox\Form\LightboxDeleteForm",
 *     },
 *     "access" = "Drupal\lightbox\LightboxAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\lightbox\LightboxHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "lightbox",
 *   data_table = "lightbox_field_data",
 *   revision_table = "lightbox_revision",
 *   revision_data_table = "lightbox_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer lightbox entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/lightbox/{lightbox}",
 *     "add-form" = "/admin/structure/lightbox/add",
 *     "edit-form" = "/admin/structure/lightbox/{lightbox}/edit",
 *     "delete-form" = "/admin/structure/lightbox/{lightbox}/delete",
 *     "version-history" = "/admin/structure/lightbox/{lightbox}/revisions",
 *     "revision" = "/admin/structure/lightbox/{lightbox}/revisions/{lightbox_revision}/view",
 *     "revision_revert" = "/admin/structure/lightbox/{lightbox}/revisions/{lightbox_revision}/revert",
 *     "translation_revert" = "/admin/structure/lightbox/{lightbox}/revisions/{lightbox_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/lightbox/{lightbox}/revisions/{lightbox_revision}/delete",
 *     "collection" = "/admin/structure/lightbox",
 *   },
 * )
 */
class Lightbox extends RevisionableContentEntityBase implements LightboxInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the lightbox owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Lightbox entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Lightbox entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['header_content'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Header Content'))
      ->setDescription(t('What do you want the header to say?'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_format',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['body_content'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Body Content'))
      ->setDescription(t('What do you want the body to say?'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Lightbox is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['fid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('File'))
      ->setDescription(t('File ID.'))
      ->setRevisionable(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
