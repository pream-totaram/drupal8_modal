<?php

namespace Drupal\lightbox\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Modal entities.
 *
 * @ingroup modal
 */
interface LightboxInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Modal name.
   *
   * @return string
   *   Name of the Modal.
   */
  public function getName();

  /**
   * Sets the Modal name.
   *
   * @param string $name
   *   The Modal name.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Modal entity.
   */
  public function setName($name);

  /**
   * Gets the Modal creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Modal.
   */
  public function getCreatedTime();

  /**
   * Sets the Modal creation timestamp.
   *
   * @param int $timestamp
   *   The Modal creation timestamp.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Modal entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Modal published status indicator.
   *
   * Unpublished Modal are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Modal is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Modal.
   *
   * @param bool $published
   *   TRUE to set this Modal to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Modal entity.
   */
  public function setPublished($published);

  /**
   * Gets the Modal revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Modal revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Modal entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Modal revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Modal revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Modal entity.
   */
  public function setRevisionUserId($uid);

}
