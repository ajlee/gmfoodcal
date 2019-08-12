<?php

namespace Drupal\gmfood_forms\Form;

use \Drupal\Core\Form\FormBase;
use \Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Session\AccountInterface;
use \Drupal\Core\Access\AccessResult;
use \Drupal\user\Entity\User;

class ModeratorAccessForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'request_moderator_access_form';
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
    public function buildForm(array $form, FormStateInterface $form_state) {

      $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      //kint()
      //


      kint($user);

      // find out if the user profile fields are complete
      $user_picture = !$user->user_picture->isEmpty();
      $field_about_me = !$user->field_about_me->isEmpty();
      $field_public_name = !$user->field_public_name->isEmpty();
      $user_phone = $user->get('field_phone')[0]->getValue();
      $user_public_email = $user->get('field_contact_email')[0]->getValue();
      $user_private_email = $user->getEmail();

      // get account link to edit profile
      $options = ['absolute' => TRUE];
      $url = \Drupal\Core\Url::fromRoute('entity.user.edit_form', ['user' => \Drupal::currentUser()->id()], $options);
      $url = $url->toString();

      kint($user_public_email);

      // check if the required form fields are completed
      // these must be filled in before requesting moderator access
      if ($field_about_me && $user_picture && $field_about_me) {

            $form['description'] = [
              '#type' => 'item',
              '#markup' => $this->t('Please explain why you want moderator access to the site.'),
            ];
            $form['description'] = [
              '#type' => 'item',
              '#markup' => $this->t('We may need to contact you to confirm your request. Currently we have the following details for you: <ul><li>Phone: ' . $user_phone['value'] . '</li><li>Account Email: ' .$user_private_email . '</li><li>Public email: ' . $user_public_email['value'] . '</ul><br/>If you need to edit these details please go to your <a href="' . $url . '">Profile Edit page</a> before submitting your request.'),
            ];
            $form['title'] = [
              '#type' => 'textarea',
              '#title' => $this->t('Title'),
              '#description' => $this->t('Please provide the reason you want access to moderate a calendar.'),
              '#required' => TRUE,
            ];
            $form['title'] = [
                  '#type' => 'textarea',
                  '#title' => $this->t('Calendars'),
                  '#description' => $this->t('Please explain which calendar you want to moderate.'),
                  '#required' => TRUE,
                ];
            $form['accept'] = array(
              '#type' => 'checkbox',
              '#title' => $this
                ->t('I accept the terms of moderating this site'),
              '#description' => $this->t('Please read and accept the terms of use'),
            );
            $form['actions'] = [
              '#type' => 'actions',
            ];
            // Add a submit button that handles the submission of the form.
            $form['actions']['submit'] = [
              '#type' => 'submit',
              '#value' => $this->t('Submit'),
            ];
      }
      //
      else {

        $form['description'] = [
          '#type' => 'item',
          '#markup' => $this->t('<h4>Please complete your account</h4>In order to be a moderator, you need to complete your profile. Please add a photo, description, and contact information, then you can request moderator access using this form. <ul><li><a href="' . $url . '">Edit your profile</a>'),
        ];
        \Drupal::messenger()->addError('You cannot request moderator access until you complete your profile.');
        \Drupal::logger('gmfood_forms')->warning('access denied to moderator form.');
      }


      return $form;

    }

    /**
       * Validate the title and the checkbox of the form
       *
       * @param array $form
       * @param \Drupal\Core\Form\FormStateInterface $form_state
       *
       */
      public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);

        $title = $form_state->getValue('title');
        $accept = $form_state->getValue('accept');

        if (strlen($title) < 10) {
          // Set an error for the form element with a key of "title".
          // $form_state->setErrorByName('title', $this->t('The title must be at least 10 characters long.'));
        }

        if (empty($accept)){
          // Set an error for the form element with a key of "accept".
          $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
        }

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

         $messenger = \Drupal::messenger();
         $messenger->addMessage('Title: '.$form_state->getValue('title'));
         $messenger->addMessage('Accept: '.$form_state->getValue('accept'));


         //
         // send email to admin: provides a link for the admin to authorise the request for calendar moderator access
         //
         $mailManager = \Drupal::service('plugin.manager.mail');
         $module = 'gmfood_forms';
         $key = 'moderator_access_request';
         $to = '';
         $emails = array();
         $langcode = 'en';
         $params['email'] = 'alexjameslee@example.com';
         $params['user'] = 'super user';
         $params['phone'] = '07988 883764';
         $params['message'] = 'dsf ohsdfoh  soihdsohso hodshf oihds oihfh';

         // get a link to edit the user profile
         $options = ['absolute' => TRUE];
         $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
         $url = \Drupal\Core\Url::fromRoute('entity.user.edit_form', ['user' => \Drupal::currentUser()->id()], $options);
         $url = $url->toString();
         $params['url'] = $url;


         // get users with manager role and email them a notification
         $ids = \Drupal::entityQuery('user')
         ->condition('status', 1)
         ->condition('roles', 'manager')
         ->execute();
         $users = User::loadMultiple($ids);
         kint($users);
         foreach($users as $user) {
           $emails[] = $user->getEmail();
         }
         // convert array to a string separated by commas
         $to = implode(",", $emails);;
         kint($emails);

         // send it!
         $send = TRUE;
         $result = $mailManager->mail($module, $key, $to, $langcode, $params, null, $send);
         var_dump($result);

         // Was the email send successful?
         if ($result['result'] !== true) {
             drupal_set_message(t('There was a problem submitting your request.'), 'error');
         } else {
             drupal_set_message(t('Your message has been sent.'));
         }
         // Redirect to home
         $form_state->setRedirect('<front>');


         // TODO: send email to admin
         // TODO: send email to user




       }

       public function access(AccountInterface $account) {
        kint($account);
        return AccessResult::allowedIf(TRUE);
       }
}
