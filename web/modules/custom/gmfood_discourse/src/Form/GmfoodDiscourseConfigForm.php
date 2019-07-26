<?php

namespace Drupal\gmfood_discourse\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configure gmfood_discourse settings for this site.
 */
class GmfoodDiscourseConfigForm extends ConfigFormBase {

    /** @var string Config settings */
  const SETTINGS = 'gmfood_discourse.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gmfood_discourse_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key for Discourse'),
      '#default_value' => $config->get('api_key'),
    ];

    $form['post_as_user'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Discourse User to Post As'),
      '#default_value' => $config->get('post_as_user'),
    ];

    $form['discourse_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base URL for the discourse site'),
      '#default_value' => $config->get('discourse_url'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

      // Retrieve the configuration
      $this->configFactory->getEditable(static::SETTINGS)

      // Set the submitted configuration setting
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('post_as_user', $form_state->getValue('post_as_user'))
      ->set('discourse_url', $form_state->getValue('discourse_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
