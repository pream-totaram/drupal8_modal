<?php

namespace Drupal\lightbox;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Modal entity.
 *
 * @see \Drupal\lightbox\Entity\Modal.
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
          return AccessResult::allowedIfHasPermission($account, 'view unpublished modal entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published modal entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit modal entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete modal entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add modal entities');
  }

}
