<?php

namespace Drupal\ccf_user_preferences\ListBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Ccf user preferences entities.
 *
 * @ingroup ccf_user_preferences
 */
class CcfUserPreferencesListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Ccf user preferences ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\ccf_user_preferences\Entity\CcfUserPreferences $entity */
    $row['id'] = Link::createFromRoute(
      $entity->id(),
      'entity.ccf_user_preferences.edit_form',
      ['ccf_user_preferences' => $entity->id()]
    );
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.ccf_user_preferences.edit_form',
      ['ccf_user_preferences' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
