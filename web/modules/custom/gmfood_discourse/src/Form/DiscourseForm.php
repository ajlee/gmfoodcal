<?php
/**
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */

namespace Drupal\gmfood_discourse\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
require_once DRUPAL_ROOT . "/modules/custom/gmfood_discourse/vendor/discourse_api/src/DiscourseAPI.php";


class DiscourseForm extends FormBase {
  /**
   * {@inheritdoc}
   */

  private $discourseApiKey = null;
  private $discourseUrl = null;
  private $discourseUser = null;
  private $discourseApi = null;

  public function __construct()
  {
    // initialise the API
    $config = \Drupal::config('gmfood_discourse.settings');
    $this->discourseUser = $config->get('post_as_user');
    $this->discourseApiKey = '174c4521e6bd349e4fca03f1bf127a1ea81787c4418cd13dcf7e26e64fbd8c5b';
    $this->discourseUrl = $config->get('discourse_url');
    $this->discourseApi = new \richp10\discourseAPI\DiscourseAPI($this->discourseUrl, $this->discourseApiKey, 'https');

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

      // Get the module configuration object
      $nodehtml = null;
      $titlehtml = null;
      $type = $node->getType();
      $success = false;

      // get the categories list
      $categories = $this->getDiscourseCategories();

      //
      // Has the form been submitted or is this the initial loading of the form?
      //
      $values = $form_state->getValues();

      // form submitted - so show the result page
      if (!empty($values)) {

        // Discourse response is in the api_result field
        if (isset($values['api_result'])) {

          $apiresult = $values['api_result']->apiresult;
          if (is_object($apiresult)) {
            // kint($values['api_result']->http_code);
            //kint($apiresult);
            //kint($apiresult->actions_summary);
            //kint($apiresult->actions_summary[0]);
            // kint($values['api_result']->apiresult->errors);

            // check for success or failure in posting to discourse
            $http_code = $values['api_result']->http_code;
            $success = ((int) $http_code >= 200) && ((int)$http_code <= 299);
            if($success) {

              // tell the user it was a success
              $message = 'Node posted to Discourse Forum!';
              drupal_set_message($message,'status');
              \Drupal::logger('gmfood_discourse')->notice($message);

              // disable the form
              $form['actions']['submit']['#disabled'] = true;
            }
            else if (isset($values['api_result']->apiresult->errors)) {

              // loop through the errors from discourse
              $errors = $apiresult->errors;

              foreach ($errors as $key => $error) {

                // output the error
                $message = 'Discourse error: ' . $error;
                drupal_set_message($message, 'error');
                \Drupal::logger('gmfood_discourse')->error($message);

                // don't show more than 5 errors (hopefully won't come to that!)
                if($key > 5) {
                  break;
                }
              }
            }
          }
          else if (is_string($apiresult)) {

            // probably api key failure
            $message = 'Discourse warning: ' . $apiresult;
            drupal_set_message($message, 'warning');
            \Drupal::logger('gmfood_discourse')->warning($message);

          }
          else {
            // unknown result from api
            // kint (gettype($apiresult));
            $message = 'Discourse warning';
            drupal_set_message($message, 'warning');
            \Drupal::logger('gmfood_discourse')->warning($message);
          }
        }
        // no api result field - something odd happening
        else {
          drupal_set_message(t('Unknown error posting to Discourse'),'error');
        }
      }

        // kint($categories);


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
        // Preprocessing newsletters
        else if ($type == "event") {
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


      /*
       * Create the form and the default values to the submitted form values
       */

      $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Post Title (at least 10 characters)'),
        '#description' => $this->t('Enter the title of the post. Note that the title should form a sentence.'),
        '#required' => TRUE,
        '#default_value' => $node->getTitle(),

      ];

      $form['category'] = [
        '#type' => 'select',
        '#title' => $this->t('Category'),
        '#description' => $this->t('The Discourse category where this post will appear'),
        '#options' => array('' => 'Please select a category'),
        '#default_value' => '',
        '#required' => TRUE,
      ];
      if (is_array($categories)) {
        // loop through the category results and add to the select box
        foreach ($categories as $key => $category) {
          $form['category']['#options'][$category->id] = $category->name;
        }
      }
      else {
        $form['category']['#options'] = array('', $this->t("Could not get Discourse categories. DiscourseAPI may be down - please try again in a few minutes."));
        $form['#default_value'] = '';
      }

      $form['intro'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Intro'),
        '#description' => $this->t('Additional text to insert at the top of the post'),
        '#default_value' => 'Please find details of the ' . $type . ' below',
      ];

      $form['nodehtml'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Content'),
        '#description' => $this->t('HTML of the ' . $type . ' node (you can edit this if you want, or even copy and paste it into an email to send manually)'),
        '#default_value' => $nodehtml,
        '#required' => TRUE,
      ];

      // Add a submit button that handles the submission of the form.
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Post to Discourse'),
      ];
      $form['actions']['#weight'] = 100;

      // if the form was submitted successfully
      if ($values && $success) {

        $form['intro'] = [
          '#markup' => '<div class="form-item"><span class="alert-success">' . t('You have already posted this content to Discourse') . '</span></div>',
          '#weight' => -50,
        ];

        // disable the form if its been posted already
        $form['title']['#disabled'] = TRUE;
        $form['intro']['#disabled'] = TRUE;
        $form['nodehtml']['#disabled'] = TRUE;
        $form['category']['#disabled'] = TRUE;
        $form['actions']['submit']['#disabled'] =TRUE;
        $form['actions']['#weight'] = 100;
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

      // Get the module configuration object
      $config = \Drupal::config('gmfood_discourse_settings');

      // Get the value of the user to post as
      $user = $config->get('post_as_user');
      if (empty($user)) {
        $user = 'drupal_admin';
      }

      // get the values
      $values = $form_state->getValues();

      // build the submission
      if (!empty($values)) {
        $title = $values['title'];
        $body = $values['intro'] . '<hr/><br/>' . $values['nodehtml'];
        $category = $values['category'];
      }


      // submit to discourse and get the result
      if(is_object($this->discourseApi)) {
        $result = $this->discourseApi->createTopic($title, $body, $category, $this->discourseUser);
        $form_state->setValue('api_result',$result);
        // kint('post');
        //kint($result);

        //kint($result->apiresult->errors);
      }

      // reload the page
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
      // TODO: validate that category, titlle, body exist
     }

    function remove_html_comments($content = '') {
      return preg_replace('/<!--(.|\s)*?-->/', '', $content);
    }


    //
    // Load the list of available categories
    // Returns -1 on failure
    // Returns an array in form [ 0...n ][ id, name ] on success
    //
    public function getDiscourseCategories () {

      $categories_result = array();

      // create API if not already there - prevents mystery bugs
      if ($this->discourseApi == null) {
        $this->discourseApi = new \richp10\discourseAPI\DiscourseAPI($this->discourseUrl, $this->discourseApiKey, 'https');
      }
      // if the result is there
      if (is_object($this->discourseApi)) {
      $result = $this->discourseApi->getCategories();
      $apiresult = $result->apiresult;
      // kint($result);
      if (is_object($apiresult)) {
         // kint('is object');
         //kint($values['api_result']->http_code);
        // kint($apiresult);
        //kint($apiresult->actions_summary);
        //kint($apiresult->actions_summary[0]);
        //kint($values['api_result']->apiresult->errors);

        // check for success or failure connecting to discourse
        $http_code = $result->http_code;
        $success = ((int) $http_code >= 200) && ((int)$http_code <= 299);

        // success
        if ($success) {
          //kint($apiresult->category_list);
          //kint($apiresult->category_list->categories);

          // loop through the categories
          if(is_array($apiresult->category_list->categories)) {

            $categories = $apiresult->category_list->categories;

            // for each category store the name and id
            foreach ($categories as $key => $category) {
              // kint($category);
              $categories_result[$key]['id'] = $category->id;
              $categories_result[$key]['name'] = $category->name;
            }
          }
          return $categories;
        }
        else if (is_array($apiresult->errors)) {

          // kint ('error...');
          // loop through the errors from discourse

          $errors = $apiresult->errors;
          // kint($apiresult->errors);

          foreach ($errors as $key => $error) {

            // output the error
            $message = 'Discourse error: ' . $error;
            drupal_set_message($message, 'error');
            \Drupal::logger('gmfood_discourse')->error($message);

            // don't show more than 5 errors (hopefully won't come to that!)
            if($key > 5) {
              break;
            }
          }
          return -1;
        }
        else {
          // unknown result from api
          // kint (gettype($apiresult));
          $message = 'Discourse warning';
          drupal_set_message($message, 'warning');
          \Drupal::logger('gmfood_discourse')->warning($message);
        }
      }
      else {

        // unknown result from api
        // kint (gettype($apiresult));
        $message = 'Discourse warning';
        drupal_set_message($message, 'warning');
        \Drupal::logger('gmfood_discourse')->warning($message);
      }
      // check for success
    }
  }
}

