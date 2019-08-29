<?php
namespace Drupal\gmfood_general\Access;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;

/**
 * Checks access for displaying user profile page
 */
class ViewModeratorProfile implements AccessInterface{

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, $user) {

    // anonymous psuedo account not accessible by anyone
    if($user == 0){
      return AccessResult::forbidden();
    }

    // user can access own profile
    $current_user = \Drupal::currentUser();
    if ($current_user->id() == $user) {
      return AccessResult::allowed();
    }


    // block public access to view the root admin account
    // this account should never be a moderator
    if($user == 1){
      return AccessResult::forbidden();
    }

    // Load the current user
    $user_entity = \Drupal\user\Entity\User::load($user);
    $public_account = $user_entity->hasPermission('profile is publicly visible');

    // does it have the permission "profile is publicly visible"
    // moderators, site manager, etc have this permission
    if($public_account){
    // Check if admin has "Administer users" permission
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
