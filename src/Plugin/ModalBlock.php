<?php

namespace Drupal\modal\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * The modal block, content inside the modal
 * 
 * @Block(
 *  id= "modal_block",
 * admin_label = @Translation("Modal Block")
 * )
 */
class ModalBlock extends BlockBase
{
    public function build()
    {
        return [
            '#markup' => $this->t('Leannah Totaram'),
        ];
    }
}