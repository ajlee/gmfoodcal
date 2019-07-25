<?php
/**
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */

namespace Drupal\gmfood_discourse\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
require_once "/var/www/drupal8/web/modules/custom/gmfood_discourse/vendor/discourse_api/src/DiscourseAPI.php";


class DiscourseForm extends FormBase {
  /**
   * {@inheritdoc}
   */

  private $discourseApiKey = null;
  private $discourseApi = null;

  public function __construct()
  {
    // initialise the API
    $this->discourseApiKey = getenv('DISCOURSE_KEY');
    $this->discourseApi = new \richp10\discourseAPI\DiscourseAPI("forum.gmfoodforum.org", $this->discourseApiKey, 'https');
  }

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

      $nodehtml = null;
      $titlehtml = null;
      $type = $node->getType();

      //
      // Has the form been submitted or is this the initial loading of the form?
      //
      $values = $form_state->getValues();

      // form submitted - so show the result page
      if (!empty($values)) {
        drupal_set_message('Node posted to Discourse Forum!');
        $form_state->setRedirect('<front>');
      }

      // form not submitted - so show the submit form
      else {
        // Preprocessing newsletters
        if ($type == "simplenews_issue") {
          $view_mode = 'email_plain';
          $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
          $build = $view_builder->view($node, $view_mode);
          $nodehtml = \Drupal::service('renderer')->renderPlain($build);

          // removes HTML comments
          $nodehtml = preg_replace('/<!--(.|\s)*?-->/', '', $nodehtml);

          // Remove blank lines - New line is required to split non-blank lines
          // https://stackoverflow.com/questions/709669/how-do-i-remove-blank-lines-from-text-in-php
          $nodehtml = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $nodehtml);
        }

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


        $form['nodehtml'] = [
          '#type' => 'textarea',
          '#title' => $this->t('HTML of the ' . $type . ' node'),
          '#default_value' => $nodehtml,
        ];

        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Post to Discourse'),
        ];
      }

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
      // Rebuild the form
      $form_state->setRebuild();
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

    function remove_html_comments($content = '') {
      return preg_replace('/<!--(.|\s)*?-->/', '', $content);
    }
}

