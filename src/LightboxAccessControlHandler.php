<?php

namespace Drupal\lightbox;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Lightbox entity.
 *
 * @see \Drupal\lightbox\Entity\Lightbox.
 */
class LightboxAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\lightbox\Entity\LightboxInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished lightbox entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published lightbox entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit lightbox entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete lightbox entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add lightbox entities');
  }

}
