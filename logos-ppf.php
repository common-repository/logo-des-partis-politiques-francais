<?php
/*
  Plugin Name: Logos des partis politiques fran&ccedil;ais
  Plugin URI: http://ecolosites.eelv.fr/articles-evenement-logosppf/
  Description: Widget qui affiche les logos et fait un lien vers les principaux partis politiques fran&ccedil;ais
  Version: 1.6.0
  Author: bastho // EÉLV
  Author URI: http://ecolosites.eelv.fr/
  License: CC BY-NC
  Text Domain: logosppf
  Domain Path: /languages
 */

add_action('wp_enqueue_scripts', array('logosppf', 'scripts'));
add_action('admin_enqueue_scripts', array('logosppf', 'scripts'));
add_action('widgets_init', array('logosppf', 'register'));
if (defined('MULTISITE') && MULTISITE == true) {
    add_action('network_admin_menu', array('logosppf', 'menu'));
}
else {
    add_action('admin_menu', array('logosppf', 'menu'));
}

class logosppf {

    public static function no_use() {
      __('Widget qui affiche les logos et fait un lien vers les principaux partis politiques fran&ccedil;ais', 'logosppf');
      __('Logos des partis politiques fran&ccedil;ais', 'logosppf');
    }

    public static function scripts($hook) {
      if(logosppf::is_widget_active()){
        wp_enqueue_style('logosppf', plugins_url('/logosppf.css', __FILE__), false, null);
      }
      if($hook=='widgets.php' || $hook=='settings_page_logosppfadmin'){
          wp_enqueue_script( 'wp-color-picker' );
          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_script('logosppf', plugins_url('/logosppf.js', __FILE__), array('jquery'), false, true);
      }
    }

    public static function register() {
      require_once (plugin_dir_path(__FILE__) . '/widget.php');
      register_widget('logosppf_widget');
    }

    public static function is_widget_active(){
      global $_wp_sidebars_widgets;
      // If loading from front page, consult $_wp_sidebars_widgets rather than options
      // to see if wp_convert_widget_settings() has made manipulations in memory.
      if ( ! is_admin() ) {
        $sidebars_widgets = $_wp_sidebars_widgets;
        if ( empty( $sidebars_widgets ) ) {
                $sidebars_widgets = get_option( 'sidebars_widgets', array() );
        }
        foreach($sidebars_widgets as $sidebar=>$widgets){
          if(is_array($widgets)){
            foreach($widgets as $widget){
              if(strstr($widget, 'logosppf_widget')){
                return true;
              }
            }
          }
        }
      }
      return false;
    }

    public static function menu() {
      add_submenu_page('settings.php', __('Logos des partis', 'logosppf'), __('Logos des partis', 'logosppf'), 'manage_option', 'logosppfadmin', array('logosppf', 'admin'));
    }

    public static function admin() {
      if ((false!== $ban = \filter_input(INPUT_POST,'logosppf_ban')) && wp_verify_nonce(\filter_input(INPUT_POST,'logosppf_nonce'), 'logosppf_nonce')) {
          $bans = implode(',',  array_unique(explode(',',$ban)));
          update_site_option('logosppf', array('ban' =>$bans));
          ?>
          <div class="updated"><p><strong><?php _e('Options sauvegardées', 'logosppf'); ?></strong></p></div>
          <?php
      }
      $logosppf = get_site_option('logosppf', array('ban' => ''));
      $logos = logosppf_widget::liste(true);
      $ban = explode(',',$logosppf['ban']);
      ?>
      <div class="wrap">
          <div id="icon-edit" class="icon32 icon32-posts-logosppf"><br/></div>
          <h2><?= _e('Logos partis', 'logosppf') ?></h2>

          <form method="post" action="#" id="logosppf_banner">
            <?php wp_nonce_field('logosppf_nonce', 'logosppf_nonce'); ?>
              <p><label class="logosppf_nojs"><?= _e('Logos bannis :', 'logosppf') ?>
                  <input  type="text" name="logosppf_ban" id="logosppf_ban" size="60"  value="<?= $logosppf['ban'] ?>" class="wide">
                <?= _e('(séparé par une virgule)', 'logosppf') ?>
                </label>
              <legend>
                <h3><?= _e('Logos disponibles', 'logosppf') ?></h3>
                <?php foreach ($logos as $logo) { ?>
                  <a class="logosppf-ban-item<?php echo(in_array($logo['name'],$ban)?' banned':''); ?>"><?php echo $logo['name'] ?></a>
                <?php } ?>
              </legend>
              </p>
            <p class="submit">
                <input type="submit" class="button button-primary" value="<?php _e('Enregistrer', 'logosppf') ?>" />
            </p>
          </form>
      </div>
      <?php
    }
}
