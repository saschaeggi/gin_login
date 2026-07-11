<?php

namespace Drupal\gin_login\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\gin\GinSettings;

/**
 * Hook implementations for gin_login.
 */
class GinLoginHooks {

  /**
   * Implements hook_preprocess_HOOK() for html.
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(&$variables) {
    if (_gin_login__check_path()) {
      // Add identification class to paths.
      $variables['attributes']['class'][] = 'gin-login';

      if (_gin_login_gin_is_active()) {
        // Get Gin settings.
        /** @var \Drupal\gin\GinSettings $settings */
        $settings = \Drupal::classResolver(GinSettings::class);

        // Set accent color.
        $variables['attributes']['data-gin-accent'] = $settings->get('preset_accent_color');

        // Set focus color.
        $variables['attributes']['data-gin-focus'] = $settings->get('preset_focus_color');

        // High contrast mode.
        if ($settings->get('high_contrast_mode')) {
          $variables['attributes']['class'][] = 'gin--high-contrast-mode';
        }
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_ginlogin')]
  public function preprocessGinlogin(&$variables) {
    $config = \Drupal::config('gin_login.settings');
    $theme = \Drupal::theme()->getActiveTheme()->getName();
    $variables['site_name'] = \Drupal::config('system.site')->get('name');
    $themeIconPath = _gin_login_theme_get_setting('icon_path', $theme);
    $themeUseDefaultIcon = _gin_login_theme_get_setting('icon_default', $theme);
    $fileUrlGenerator = \Drupal::service('file_url_generator');

    // #3258015: Make sure we're compatible with the new variant.
    if ($themeUseDefaultIcon === NULL) {
      $themeIconPath = _gin_login_theme_get_setting('logo.path', $theme);
      $themeUseDefaultIcon = _gin_login_theme_get_setting('logo.use_default', $theme);
    }

    if (!$themeUseDefaultIcon && !empty($themeIconPath)) {
      $variables['icon_path'] = $fileUrlGenerator->generateAbsoluteString($themeIconPath);
    }

    $configData = $config->get();

    if (isset($configData['logo']) && $configData['logo']['use_default'] == "0") {
      if (isset($configData['logo']['path']) && !empty($configData['logo']['path'])) {
        $variables['icon_path'] = $fileUrlGenerator->generateAbsoluteString($configData['logo']['path']);
      }
    }

    if (isset($configData['brand_image']) && $configData['brand_image']['use_default'] == "0") {
      if (isset($configData['brand_image']['path']) && !empty($configData['brand_image']['path'])) {
        $variables['brand_image'] = [
          '#theme' => 'image',
          '#uri' => $configData['brand_image']['path'],
          '#attributes' => ['loading' => 'lazy'],
        ];
      }
    }
    else {
      $variables['brand_image'] = '';
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for 'page'.
   */
  #[Hook('theme_suggestions_page_alter')]
  public function themeSuggestionsPageAlter(array &$suggestions, array $variables) {
    if (!_gin_login_gin_is_active() && !_gin_login_default_admin_is_active()) {
      return;
    }

    // Get path from Route.
    $route = \Drupal::routeMatch()->getRouteName();

    switch ($route) {
      case 'user.login':
        $suggestions[] = 'page__user__login';
        break;

      case 'user.pass':
      case 'user.reset.form':
        $suggestions[] = 'page__user__password';
        break;

      case 'user.register':
        $suggestions[] = 'page__user__register';
        break;

      case 'user.logout.confirm':
        $suggestions[] = 'page__user__logout_confirm';
        break;
    }
  }

}
