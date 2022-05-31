<?php

namespace Drupal\ccf_user_preferences\Storage;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CcfUserPreferencesStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Ccf user preferences revision IDs for a specific Ccf user preferences.
   *
   * @param \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface $entity
   *   The Ccf user preferences entity.
   *
   * @return int[]
   *   Ccf user preferences revision IDs (in ascending order).
   */
  public function revisionIds(CcfUserPreferencesInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Ccf user preferences author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Ccf user preferences revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface $entity
   *   The Ccf user preferences entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CcfUserPreferencesInterface $entity);

  /**
   * Unsets the language for all Ccf user preferences with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
