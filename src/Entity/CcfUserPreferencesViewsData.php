<?php

namespace Drupal\ccf_user_preferences\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Ccf user preferences entities.
 */
class CcfUserPreferencesViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
