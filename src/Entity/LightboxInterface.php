<?php

namespace Drupal\lightbox\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Lightbox entities.
 *
 * @ingroup lightbox
 */
interface LightboxInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Lightbox name.
   *
   * @return string
   *   Name of the Lightbox.
   */
  public function getName();

  /**
   * Sets the Lightbox name.
   *
   * @param string $name
   *   The Lightbox name.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Lightbox entity.
   */
  public function setName($name);

  /**
   * Gets the Lightbox creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Lightbox.
   */
  public function getCreatedTime();

  /**
   * Sets the Lightbox creation timestamp.
   *
   * @param int $timestamp
   *   The Lightbox creation timestamp.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Lightbox entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Lightbox published status indicator.
   *
   * Unpublished Lightbox are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Lightbox is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Lightbox.
   *
   * @param bool $published
   *   TRUE to set this Lightbox to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Lightbox entity.
   */
  public function setPublished($published);

  /**
   * Gets the Lightbox revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Lightbox revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Lightbox entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Lightbox revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Lightbox revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\lightbox\Entity\LightboxInterface
   *   The called Lightbox entity.
   */
  public function setRevisionUserId($uid);

}
