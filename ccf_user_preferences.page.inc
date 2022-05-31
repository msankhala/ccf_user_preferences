<?php

/**
 * @file
 * Contains ccf_user_preferences.page.inc.
 *
 * Page callback for Ccf user preferences entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Ccf user preferences templates.
 *
 * Default template: ccf_user_preferences.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_ccf_user_preferences(array &$variables) {
  // Fetch CcfUserPreferences Entity Object.
  $ccf_user_preferences = $variables['elements']['#ccf_user_preferences'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
