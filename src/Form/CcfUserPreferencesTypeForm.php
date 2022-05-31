<?php

namespace Drupal\ccf_user_preferences\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CcfUserPreferencesTypeForm.
 */
class CcfUserPreferencesTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $ccf_user_preferences_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $ccf_user_preferences_type->label(),
      '#description' => $this->t("Label for the Ccf user preferences type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $ccf_user_preferences_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ccf_user_preferences\Entity\CcfUserPreferencesType::load',
      ],
      '#disabled' => !$ccf_user_preferences_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $ccf_user_preferences_type = $this->entity;
    $status = $ccf_user_preferences_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Ccf user preferences type.', [
          '%label' => $ccf_user_preferences_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Ccf user preferences type.', [
          '%label' => $ccf_user_preferences_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($ccf_user_preferences_type->toUrl('collection'));
  }

}
