<?php

namespace Drupal\lightbox\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\lightbox\Entity\LightboxInterface;

/**
 * Class LightboxController.
 *
 *  Returns responses for Lightbox routes.
 */
class LightboxController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Lightbox  revision.
   *
   * @param int $lightbox_revision
   *   The Lightbox  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($lightbox_revision) {
    $lightbox = $this->entityManager()->getStorage('lightbox')->loadRevision($lightbox_revision);
    $view_builder = $this->entityManager()->getViewBuilder('lightbox');

    return $view_builder->view($lightbox);
  }

  /**
   * Page title callback for a Lightbox  revision.
   *
   * @param int $lightbox_revision
   *   The Lightbox  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($lightbox_revision) {
    $lightbox = $this->entityManager()->getStorage('lightbox')->loadRevision($lightbox_revision);
    return $this->t('Revision of %title from %date', ['%title' => $lightbox->label(), '%date' => format_date($lightbox->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Lightbox .
   *
   * @param \Drupal\lightbox\Entity\LightboxInterface $lightbox
   *   A Lightbox  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(LightboxInterface $lightbox) {
    $account = $this->currentUser();
    $langcode = $lightbox->language()->getId();
    $langname = $lightbox->language()->getName();
    $languages = $lightbox->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $lightbox_storage = $this->entityManager()->getStorage('lightbox');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $lightbox->label()]) : $this->t('Revisions for %title', ['%title' => $lightbox->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all lightbox revisions") || $account->hasPermission('administer lightbox entities')));
    $delete_permission = (($account->hasPermission("delete all lightbox revisions") || $account->hasPermission('administer lightbox entities')));

    $rows = [];

    $vids = $lightbox_storage->revisionIds($lightbox);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\lightbox\LightboxInterface $revision */
      $revision = $lightbox_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $lightbox->getRevisionId()) {
          $link = $this->l($date, new Url('entity.lightbox.revision', ['lightbox' => $lightbox->id(), 'lightbox_revision' => $vid]));
        }
        else {
          $link = $lightbox->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.lightbox.translation_revert', ['lightbox' => $lightbox->id(), 'lightbox_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.lightbox.revision_revert', ['lightbox' => $lightbox->id(), 'lightbox_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.lightbox.revision_delete', ['lightbox' => $lightbox->id(), 'lightbox_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['lightbox_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }
  
  public function content()
  {
    return [
      '#theme' => 'output',
      '#var' => $this->t('Hello')
    ];
  }

}
