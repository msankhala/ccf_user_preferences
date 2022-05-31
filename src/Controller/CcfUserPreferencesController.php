<?php

namespace Drupal\ccf_user_preferences\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CcfUserPreferencesController.
 *
 *  Returns responses for Ccf user preferences routes.
 */
class CcfUserPreferencesController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Ccf user preferences revision.
   *
   * @param int $ccf_user_preferences_revision
   *   The Ccf user preferences revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($ccf_user_preferences_revision) {
    $ccf_user_preferences = $this->entityTypeManager()->getStorage('ccf_user_preferences')
      ->loadRevision($ccf_user_preferences_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('ccf_user_preferences');

    return $view_builder->view($ccf_user_preferences);
  }

  /**
   * Page title callback for a Ccf user preferences revision.
   *
   * @param int $ccf_user_preferences_revision
   *   The Ccf user preferences revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($ccf_user_preferences_revision) {
    $ccf_user_preferences = $this->entityTypeManager()->getStorage('ccf_user_preferences')
      ->loadRevision($ccf_user_preferences_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $ccf_user_preferences->label(),
      '%date' => $this->dateFormatter->format($ccf_user_preferences->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Ccf user preferences.
   *
   * @param \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface $ccf_user_preferences
   *   A Ccf user preferences object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CcfUserPreferencesInterface $ccf_user_preferences) {
    $account = $this->currentUser();
    $ccf_user_preferences_storage = $this->entityTypeManager()->getStorage('ccf_user_preferences');

    $langcode = $ccf_user_preferences->language()->getId();
    $langname = $ccf_user_preferences->language()->getName();
    $languages = $ccf_user_preferences->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $ccf_user_preferences->label()]) : $this->t('Revisions for %title', ['%title' => $ccf_user_preferences->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all ccf user preferences revisions") || $account->hasPermission('administer ccf user preferences entities')));
    $delete_permission = (($account->hasPermission("delete all ccf user preferences revisions") || $account->hasPermission('administer ccf user preferences entities')));

    $rows = [];

    $vids = $ccf_user_preferences_storage->revisionIds($ccf_user_preferences);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ccf_user_preferences\CcfUserPreferencesInterface $revision */
      $revision = $ccf_user_preferences_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $ccf_user_preferences->getRevisionId()) {
          $link = $this->l($date, new Url('entity.ccf_user_preferences.revision', [
            'ccf_user_preferences' => $ccf_user_preferences->id(),
            'ccf_user_preferences_revision' => $vid,
          ]));
        }
        else {
          $link = $ccf_user_preferences->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
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
              Url::fromRoute('entity.ccf_user_preferences.translation_revert', [
                'ccf_user_preferences' => $ccf_user_preferences->id(),
                'ccf_user_preferences_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.ccf_user_preferences.revision_revert', [
                'ccf_user_preferences' => $ccf_user_preferences->id(),
                'ccf_user_preferences_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.ccf_user_preferences.revision_delete', [
                'ccf_user_preferences' => $ccf_user_preferences->id(),
                'ccf_user_preferences_revision' => $vid,
              ]),
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

    $build['ccf_user_preferences_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
