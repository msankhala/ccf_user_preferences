<?php

namespace Drupal\ccf_user_preferences\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface;

/**
 * Defines the storage handler class for Ccf user preferences entities.
 *
 * This extends the base storage class, adding required special handling for
 * Ccf user preferences entities.
 *
 * @ingroup ccf_user_preferences
 */
class CcfUserPreferencesStorage extends SqlContentEntityStorage implements CcfUserPreferencesStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CcfUserPreferencesInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {ccf_user_preferences_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {ccf_user_preferences_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CcfUserPreferencesInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {ccf_user_preferences_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('ccf_user_preferences_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
