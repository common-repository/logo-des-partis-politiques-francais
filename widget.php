<?php
class logosppf_widget extends WP_Widget {

    public static $sizes;
    public static $logospath;
    public static $resizedpath;
    public static $exts;
    public static $ban;

    function __construct() {
      parent::__construct(false, __('Logo parti', 'logosppf'), array('description' => __('Affiche les logos des principaux partis politiques fran&ccedil;ais', 'logosppf')));
      self::$sizes = array(
          'small' => array(35, 35, __('Thumbnail', 'default')),
          'medium' => array(75, 75, __('Medium')),
          'large' => array(125, 125, __('Large')),
            'full'=> array(NULL, NULL, __('Full Size'))
      );
      self::$logospath = plugin_dir_path(__FILE__) . 'logos/';
      self::$resizedpath = plugin_dir_path(__FILE__) . 'logos/resized/';
      self::$exts = array('gif', 'jpg', 'png');
      $logosppf = get_site_option('logosppf', array('ban' => ''));
      self::$ban = explode(',', $logosppf['ban']);
    }
    public function logosppf_widget(){
        $this->__construct();
    }

    public static function get($file,$no_ban=false) {
      $filename = basename($file);
      $parti = substr($filename, 0, strpos($filename, '_'));
      if ($file != '' && is_file($file) && in_array(substr($file, -3), self::$exts) && ($no_ban || !in_array($parti, self::$ban))) {

          return array(
            'abspath' => plugin_dir_path(__FILE__) . '/logos/' . $filename,
            'path' => plugins_url('/logos/' . $filename, __FILE__),
            'file' => $filename,
            'url' => substr($filename, strpos($filename, '_') + 1, -4),
            'name' => $parti,
          );
      }
      return false;
    }

    public static function resize($file, $width = '', $height = '', $displaysize=false) {
      $original_path = self::$logospath . $file;
      $resizedname = $file . '-' . $width . '-' . $height . '.' . substr($original_path, -3);
      $resizedfile_path = self::$resizedpath . $resizedname;
      $resizedfile_url = plugins_url('/logos/resized/' . $resizedname, __FILE__);
      if (!is_file($original_path)) {
          return false;
      }
      $size = getimagesize($original_path);
      if(!function_exists('imagecreatetruecolor') || $displaysize=='full'){
          $size[0]=$width;
          $size[1]=$height;
          return array('url' => plugins_url('/logos/' . $file, __FILE__), 'color' => false, 'size' => $size);
      }
      $function = 'image' . str_replace('jpg', 'jpeg', self::$exts[$size[2] - 1]);
      $func = 'imagecreatefrom' . str_replace('jpg', 'jpeg', self::$exts[$size[2] - 1]);

      if (!is_file($resizedfile_path)) {

          if ($size[2] == 1) {
            $src = imagecreatefromgif($original_path);
          }
          elseif ($size[2] == 2) {
            $src = imagecreatefromjpeg($original_path);
          }
          elseif ($size[2] == 3) {
            $src = imagecreatefrompng($original_path);
          }
          $neww = $size[0];
          $newh = $size[1];
          $ratio = 1;
          if (!empty($width)) {
            $jeveuxW = $width;
            if ($neww > $width) {
                $ratio = $neww / $width;
            }
          }
          if (!empty($height)) {
            $jeveuxH = $height;
            if ($newh > $height) {
                $ratio = $newh / $height;
            }
          }
          $neww /= $ratio;
          $newh /= $ratio;
          if ($neww > $width) {
            $ratio = $neww / $width;
            $neww /= $ratio;
            $newh /= $ratio;
          }

          $des = imagecreatetruecolor($neww, $newh);
          if ($des && $src) {
            imagecopyresampled($des, $src, 0, 0, 0, 0, $neww, $newh, $size[0], $size[1]);
            if (!$function($des, $resizedfile_path)) {
                return false;
            }
          }
      }
      $color_tran = array('red' => 255, 'green' => 255, 'blue' => 255);
      $size = getimagesize($resizedfile_path);
      if (function_exists($func)) {
          $im = $func($resizedfile_path);
          if ($im) {
            $color_index = imagecolorat($im, 1, 1);
            $color_tran = imagecolorsforindex($im, $color_index);
          }
      }
      return array('url' => $resizedfile_url, 'color' => $color_tran, 'size' => $size);
    }

    public static function liste($no_ban=false) {
      $logos = array();
      if (is_dir(self::$logospath)) {
          $files = scandir(self::$logospath);
          foreach ($files as $file) {
            if (false !== $logo = self::get(self::$logospath . $file, $no_ban)) {
                $logos[] = $logo;
            }
          }
      }
      return $logos;
    }

    function widget($args, $instance) {
      extract($args);
      $parti = (isset($instance['parti']) && !empty($instance['parti'])) ? $instance['parti'] : '';
      if (false !== $parti = self::get($parti)) {
          $size = (isset($instance['size']) && !empty($instance['size'])) ? $instance['size'] : 'medium';
          $title = (isset($instance['title']) && !empty($instance['title'])) ? $instance['title'] : '';
          $background = (isset($instance['background']) && !empty($instance['background'])) ? $instance['background'] : '';
          $shape = (isset($instance['shape']) && !empty($instance['shape'])) ? $instance['shape'] : 'square';
            $class = (isset($instance['class']) && !empty($instance['class'])) ? $instance['class'] : '';

          $width = (isset($instance['width']) && !empty($instance['width'])) ? $instance['width'] : self::$sizes[$size][0];
          $height = (isset($instance['height']) && !empty($instance['height'])) ? $instance['height'] : self::$sizes[$size][1];
          $link = (isset($instance['link']) && !empty($instance['link'])) ? $instance['link'] : 'http://' . $parti['url'];
            $target = (isset($instance['target']) && !empty($instance['target'])) ? $instance['target'] : '_blank';

          if (false !== $img = self::resize($parti['file'], $width, $height, $size)) {
            $box_b = !empty($args['before_widget'])?$args['before_widget']:'<div>';
            $box_a = !empty($args['after_widget'])?$args['after_widget']:'</div>';
            if(!empty($background) || !empty($instance['width']) || !empty($instance['height'])){
                $add_style = (!empty($background) ? 'background:'.$background.';' : '')
                        . ((isset($instance['width']) && !empty($instance['width'])) ? 'width:' . $width . 'px;' : '')
                        . ((isset($instance['height']) && !empty($instance['height'])) ? 'height:' . $height . 'px;' : '');
                if(strstr($box_b,'style=')){
                  $box_b=  str_replace('style="', 'style="'.$add_style, $box_b);
                  $box_b=  str_replace('style=\'', 'style=\''.$add_style, $box_b);
                }
                else{
                  $box_b=str_replace('>',' style="'.$add_style.'">',$box_b);
                }
            }
                if(!empty($class)){
                if(strstr($box_b,'class=')){
                  $box_b=  str_replace('class="', 'class="'.$class.' ', $box_b);
                  $box_b=  str_replace('class=\'', 'class=\''.$class.' ', $box_b);
                }
                else{
                  $box_b=str_replace('>',' class="'.$class.'">',$box_b);
                }
            }
            echo $box_b;
            if (!empty($title)) {
                echo $args['before_title'];
                echo $title;
                echo $args['after_title'];
            }
            echo '
                <a href="' . $link . '" target="'.$target.'" class="lppf ' . $size . ' lppf-'.$shape.'" style="'
                        . ($img['color']?'background:rgb(' . $img['color']['red'] . ',' . $img['color']['green'] . ',' . $img['color']['blue'].')':'')
                        . '">
                <img src="' . $img['url'] . '" alt="logo ' . $parti['name'] . '" style="margin:' . round(($height - $img['size'][1]) / 2) . 'px auto;"/>
                </a>';
            echo $box_a;
          }
      }
    }

    function update($new_instance, $old_instance) {
      return $new_instance;
    }

    function form($instance) {

      /* The Widget Title Itself */
      $title = (isset($instance['title']) && !empty($instance['title'])) ? $instance['title'] : '';

      /* The movement */
      $parti = (isset($instance['parti']) && !empty($instance['parti'])) ? $instance['parti'] : '';

      /* The size */
      $size = (isset($instance['size']) && !empty($instance['size'])) ? $instance['size'] : 'medium';
      $width = (isset($instance['width']) && !empty($instance['width'])) ? $instance['width'] : '';
      $height = (isset($instance['height']) && !empty($instance['height'])) ? $instance['height'] : '';

      /* The style */
      $background = (isset($instance['background']) && !empty($instance['background'])) ? $instance['background'] : '';
      $shape = (isset($instance['shape']) && !empty($instance['shape'])) ? $instance['shape'] : 'square';
      $class = (isset($instance['class']) && !empty($instance['class'])) ? $instance['class'] : '';

      /* The link */
      $link = (isset($instance['link']) && !empty($instance['link'])) ? $instance['link'] : '';
      $target = (isset($instance['target']) && !empty($instance['target'])) ? $instance['target'] : '_blank';

      $placeholdlink = '';
      ?>
      <input type="hidden" id="<?php echo $this->get_field_id('title'); ?>-title" value="<?php echo $title; ?>">
      <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre', 'logosppf'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          </label>
      </p>

      <p>
          <label for="<?php echo $this->get_field_id('parti'); ?>"><?php _e('Parti', 'logosppf'); ?>
                   <select  id="<?php echo $this->get_field_id('parti'); ?>" name="<?php echo $this->get_field_name('parti'); ?>">
                <option value=''> </option>
      <?php
      $logos = self::liste();
      foreach ($logos as $logo) {
          ?>
                    <option value="<?= $logo['abspath'] ?>" <?php
          if ($logo['abspath'] == $parti) {
            echo'selected';
            $placeholdlink = $logo['url'];
          }
          ?>><?= $logo['name'] ?></option>
      <?php } ?>
            </select>
          </label>
      </p>
      <p>
          <label for="<?php echo $this->get_field_id('shape'); ?>"><?php _e('Forme', 'logosppf'); ?>
                   <select  id="<?php echo $this->get_field_id('shape'); ?>" name="<?php echo $this->get_field_name('shape'); ?>">
                <option value='square' <?php selected('square',$shape) ?>><?php _e('Carré', 'logosppf'); ?></option>
                <option value='disc' <?php selected('disc',$shape) ?>><?php _e('Rond', 'logosppf'); ?></option>
            </select>
          </label>
      </p>
      <p>
          <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Taille', 'logosppf'); ?>
                   <select  id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
                <option value=''> </option>
      <?php foreach (self::$sizes as $siz => $dim) { ?>
                    <option value="<?= $siz ?>" <?php if ($siz == $size) {
            echo'selected';
          } ?>><?php echo $dim[2] ?> <?php echo ($dim[0]?'('.$dim[0].'x'.$dim[1].')':''); ?></option>
      <?php } ?>
            </select>
          </label>
      </p>
      <p>
          <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Lien personnalis&eacute;', 'logosppf'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" placeholder="http://<?php echo $placeholdlink; ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
         </label>
      </p>
      <p>
          <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Destination', 'logosppf'); ?>
            <select class="widefat" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>">
                    <option value="_blank" <?php selected($target, '_blank', true); ?>><?php _e('Nouvelle fenetre', 'logosppf'); ?></option>
                    <option value="_self" <?php selected($target, '_self', true); ?>><?php _e('Interne', 'logosppf'); ?></option>
                </select>
          </label>
      </p>

      <p>
      <?php _e('Boite personnalis&eacute;e :', 'logosppf'); ?>
      </p>

      <p>
          <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('Couleur de fond', 'logosppf'); ?>
            <input class="widefat logosppf-color-picker color-picker-hex wp-color-picker" id="<?php echo $this->get_field_id('background'); ?>" name="<?php echo $this->get_field_name('background'); ?>" type="text" value="<?php echo $background; ?>" onfocus="jQuery(this).wpColorPicker();" />
          </label>
      </p>
      <p>
          <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Largeur', 'logosppf'); ?>
            <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="number" value="<?php echo $width; ?>" />
            px
          </label><br>
          <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Hauteur', 'logosppf'); ?>
            <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="number" value="<?php echo $height; ?>" />
            px
          </label>
      </p>
        <p>
          <label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('Classe', 'logosppf'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo $class; ?>"/>
          </label>
      </p>
      <?php
    }
}
