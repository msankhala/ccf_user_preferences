<?php

namespace Drupal\ccf_user_preferences\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Ccf user preferences type entity.
 *
 * @ConfigEntityType(
 *   id = "ccf_user_preferences_type",
 *   label = @Translation("Ccf user preferences type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ccf_user_preferences\ListBuilder\CcfUserPreferencesTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ccf_user_preferences\Form\CcfUserPreferencesTypeForm",
 *       "edit" = "Drupal\ccf_user_preferences\Form\CcfUserPreferencesTypeForm",
 *       "delete" = "Drupal\ccf_user_preferences\Form\CcfUserPreferencesTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ccf_user_preferences\Route\CcfUserPreferencesTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "ccf_user_preferences_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "ccf_user_preferences",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/people/ccf_user_preferences_type/{ccf_user_preferences_type}",
 *     "add-form" = "/admin/people/ccf_user_preferences_type/add",
 *     "edit-form" = "/admin/people/ccf_user_preferences_type/{ccf_user_preferences_type}/edit",
 *     "delete-form" = "/admin/people/ccf_user_preferences_type/{ccf_user_preferences_type}/delete",
 *     "collection" = "/admin/people/ccf_user_preferences_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "bundle",
 *     "uid",
 *   }
 * )
 */
class CcfUserPreferencesType extends ConfigEntityBundleBase implements CcfUserPreferencesTypeInterface {

  /**
   * The Ccf user preferences type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Ccf user preferences type label.
   *
   * @var string
   */
  protected $label;

}
