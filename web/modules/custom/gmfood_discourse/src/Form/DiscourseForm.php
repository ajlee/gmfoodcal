<?php
/**
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */
namespace Drupal\gmfood_discourse\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

class DiscourseForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'discourse_form';
  }

   /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {

      $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('New Post Title'),
        '#description' => $this->t('Enter the title of the post. Note that the title must be at least 10 characters in length.'),
        '#required' => TRUE,
        '#default_value' => $node->getTitle(),
      ];

      $form['category'] = [
        '#type' => 'select',
        '#title' => $this->t('Select category to post to'),
        '#options' => [
          '1' => $this->t('Development'),
          '2' => $this->t('Events'),
          '3' => $this->t('Food Waste'),
        ],
      ];

      // Add a submit button that handles the submission of the form.
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Post to Discourse'),
      ];

      return $form;

    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

      // Display the results.

      // Call the Static Service Container wrapper
      // We should inject the messenger service, but its beyond the scope of this example.
      $messenger = \Drupal::messenger();
      $messenger->addMessage('Title: '.$form_state->getValue('title'));
      $messenger->addMessage('Accept: '.$form_state->getValue('accept'));

      // Redirect to home
      $form_state->setRedirect('<front>');

    }

    /**
      * Validate the title and the checkbox of the form
      *
      * @param array $form
      * @param \Drupal\Core\Form\FormStateInterface $form_state
      *
      */
     public function validateForm(array &$form, FormStateInterface $form_state) {

     }
}
