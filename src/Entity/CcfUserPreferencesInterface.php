<?php

namespace Drupal\ccf_user_preferences\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Ccf user preferences entities.
 *
 * @ingroup ccf_user_preferences
 */
interface CcfUserPreferencesInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Ccf user preferences name.
   *
   * @return string
   *   Name of the Ccf user preferences.
   */
  public function getName();

  /**
   * Sets the Ccf user preferences name.
   *
   * @param string $name
   *   The Ccf user preferences name.
   *
   * @return \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface
   *   The called Ccf user preferences entity.
   */
  public function setName($name);

  /**
   * Gets the Ccf user preferences creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Ccf user preferences.
   */
  public function getCreatedTime();

  /**
   * Sets the Ccf user preferences creation timestamp.
   *
   * @param int $timestamp
   *   The Ccf user preferences creation timestamp.
   *
   * @return \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface
   *   The called Ccf user preferences entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Ccf user preferences revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Ccf user preferences revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface
   *   The called Ccf user preferences entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Ccf user preferences revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Ccf user preferences revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ccf_user_preferences\Entity\CcfUserPreferencesInterface
   *   The called Ccf user preferences entity.
   */
  public function setRevisionUserId($uid);

}
