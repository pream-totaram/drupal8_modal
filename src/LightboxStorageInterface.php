<?php

namespace Drupal\lightbox;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface LightboxStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Lightbox revision IDs for a specific Lightbox.
   *
   * @param \Drupal\lightbox\Entity\LightboxInterface $entity
   *   The Lightbox entity.
   *
   * @return int[]
   *   Lightbox revision IDs (in ascending order).
   */
  public function revisionIds(LightboxInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Lightbox author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Lightbox revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\lightbox\Entity\LightboxInterface $entity
   *   The Lightbox entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(LightboxInterface $entity);

  /**
   * Unsets the language for all Lightbox with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
