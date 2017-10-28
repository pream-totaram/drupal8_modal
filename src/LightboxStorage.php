<?php

namespace Drupal\lightbox;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\lightbox\Entity\LightboxInterface;

/**
 * Defines the storage handler class for Lightbox entities.
 *
 * This extends the base storage class, adding required special handling for
 * Lightbox entities.
 *
 * @ingroup lightbox
 */
class LightboxStorage extends SqlContentEntityStorage implements LightboxStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(LightboxInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {lightbox_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {lightbox_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(LightboxInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {lightbox_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('lightbox_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
