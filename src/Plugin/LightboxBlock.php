<?php

namespace Drupal\lightbox\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * The lightbox block, content inside the lightbox
 * 
 * @Block(
 *  id= "lightbox_block",
 * admin_label = @Translation("Lightbox Block")
 * )
 */
class LightboxBlock extends BlockBase
{
    public function build()
    {
        return [
            '#markup' => $this->t('Leannah Totaram'),
        ];
    }
}