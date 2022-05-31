<?php

namespace Drupal\ccf_user_preferences\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Ccf user preferences revision.
 *
 * @ingroup ccf_user_preferences
 */
class CcfUserPreferencesRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Ccf user preferences revision.
   *
   * @var \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface
   */
  protected $revision;

  /**
   * The Ccf user preferences storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $ccfUserPreferencesStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->ccfUserPreferencesStorage = $container->get('entity_type.manager')->getStorage('ccf_user_preferences');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ccf_user_preferences_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => \Drupal::service('date.formatter')->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.ccf_user_preferences.version_history', ['ccf_user_preferences' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $ccf_user_preferences_revision = NULL) {
    $this->revision = $this->CcfUserPreferencesStorage->loadRevision($ccf_user_preferences_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->CcfUserPreferencesStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Ccf user preferences: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Ccf user preferences %title has been deleted.', ['%revision-date' => \Drupal::service('date.formatter')->format($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.ccf_user_preferences.canonical',
       ['ccf_user_preferences' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {ccf_user_preferences_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.ccf_user_preferences.version_history',
         ['ccf_user_preferences' => $this->revision->id()]
      );
    }
  }

}
