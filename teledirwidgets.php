<?php
/*
Plugin Name: TeledirWidgets
Plugin URI: http://www.teledir.de/wordpress-plugins
Description: Displays informations according to the ip of the visitor like the county, the city, a map, the ip and the user agent as a nice summup in the sidebar of your blog via widget interface or anywhere else via function call. Check out more <a href="http://www.teledir.de/wordpress-plugins">Wordpress Plugins</a> and <a href="http://www.teledir.de/widgets">Widgets</a>.
Version: 0.1
Author: teledir
Author URI: http://www.teledir.de
*/
 
/**
 * v0.1 16.06.2009 initial release
 */

class TeledirWidgets {
  var $id;
  var $title;
  var $plugin_url;
  var $version;
  var $name;
  var $url;
  var $options;
  var $locale;
  var $widgets;
  
  function TeledirWidgets() {
    $this->id         = 'teledirwidgets';
    $this->title      = 'TeledirWidgets';
    $this->version    = '0.1';
    $this->plugin_url = 'http://www.teledir.de/wordpress-plugins';
    $this->name       = 'TeledirWidgets v'. $this->version;
    $this->url        = get_bloginfo('wpurl'). '/wp-content/plugins/' . $this->id;

	  $this->locale     = get_locale();
    $this->path       = dirname(__FILE__);
	  if(empty($this->locale)) {
		  $this->locale = 'en_US';
    }

    load_textdomain($this->id, sprintf('%s/%s.mo', $this->path, $this->locale));
    
    $this->widgets = array(
      1 => array(
        'language'  => '',
        'width'     => 160,
        'height'    => 215,
        'features'  => 'Country, City, Browser, Map, IP, Operation System, Icons',
        'code'      => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><div>Widget
 by <a href="http://www.teledir.de">Teledir</a></div></div>'
      ),
      5 => array(
        'language' => '',
        'width' => 468,
        'height' => 60,
        'features' => 'Country, City, Browser, Map, IP, Operation System, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><a href="http://www.teledir.de" title="Teledir"><img src="http://syndication.teledir.de/widgets/v2/img/logo10x50.gif" width="10" height="50" alt="Teledir" title="Teledir" border="0" /></a></div>'
      ),
      6 => array(
        'language' => '',
        'width' => 728,
        'height' => 90,
        'features' => 'Country, City, Browser, Map, IP, Operation System, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><a href="http://www.teledir.de" title="Teledir"><img src="http://syndication.teledir.de/widgets/v2/img/logo10x80.gif" width="10" height="80" alt="Teledir" title="Teledir" border="0" /></a></div>'
      ),
      7 => array(
        'language' => '',
        'width' => 250,
        'height' => 200,
        'features' => 'Country, City, Browser, Map, IP, Operation System, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><div>Widget
 by <a href="http://www.teledir.de">Teledir</a></div></div>'
      ),
      8 => array(
        'language' => '',
        'width' => 125,
        'height' => 125,
        'features' => 'Country, City, Browser, IP, Operation System, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><div>Widget
 by <a href="http://www.teledir.de">Teledir</a></div></div>'
      ),
      9 => array(
        'language' => '',
        'width' => 120,
        'height' => 175,
        'features' => 'Map, IP, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><div>Widget
 by <a href="http://www.teledir.de">Teledir</a></div></div>'
      ),
      3 => array(
        'language' => __('german', $this->id),
        'width' => 468,
        'height' => 60,
        'features' => 'City, Browser, IP, Operation System',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><a href="http://www.teledir.de" title="Teledir"><img src="http://syndication.teledir.de/widgets/v2/img/logo10x50.gif" width="10" height="50" alt="Teledir" title="Teledir" border="0" /></a></div>'
      ),
      4 => array(
        'language' => __('german', $this->id),
        'width' => 468,
        'height' => 60,
        'features' => 'City, Browser, IP, Operation System, Icons',
        'code' => '<div><script type="text/javascript" src="http://syndication.teledir.de/widgets/v2/loader.js?target=1&[PARAMS]"></script><a href="http://www.teledir.de" title="Teledir"><img src="http://syndication.teledir.de/widgets/v2/img/logo10x50.gif" width="10" height="50" alt="Teledir" title="Teledir" border="0" /></a></div>'
      )
    );

    $this->loadOptions();

    if(!is_admin()) {
      add_filter('wp_head', array(&$this, 'blogHeader'));
    }
    else {
      if(stripos($_SERVER[ 'REQUEST_URI' ], $this->id) !== false ) {
        add_action( 'admin_head', array( &$this, 'adminHeader' ) );
      }

      add_action('admin_menu', array(&$this, 'optionMenu')); 
    }

    add_action('widgets_init', array( &$this, 'initWidget')); 
  }

  function optionMenu() {
    add_options_page($this->title, $this->title, 8, __FILE__, array(&$this, 'optionMenuPage'));
  }
  
  function adminHeader() {
    printf( '<style rel="stylesheet" href="%s/styles/colorpicker.css" type="text/css" />', $this->url );
    printf( '<script type="text/javascript">if(!window.jQuery)document.write("<script type=\"text/javascript\" src=\"%s/js/jquery.js\">"+"<"+"/script>");</script>', $this->url );
    printf( '<script type="text/javascript" src="%s/js/colorpicker.js"></script>', $this->url );
echo '<script type="text/javascript">
jQuery(function(jQuery) {
  jQuery(".colorpicker").attachColorPicker();
  jQuery( ".colorpicker" ).keyup(function() {
    jQuery.colorPicker.hideColorPicker();
    var v = jQuery(this).getValue();
    if( v && v.length == 7 ) {
      jQuery(this).setSpanColor( jQuery(this).getValue() );
    }
  });
});
</script>';
  }
  
  function getSelectbox($name, $items, $selected) {
    $data = '';

    foreach($items as $k => $v) {
      $title = sprintf("%dx%d%s - %s", $v['width'], $v['height'], empty($v['language']) ? '' : sprintf(__(' (%s only)', $this->id), $v['language']), $v['features']);
      $data .= sprintf('<option value="%s"%s>%s</option>', $k, $k == $selected ? ' "selected="selected"' : '', $title);
    }

    return '<select onchange="javascript:Teledirwidgets.change(this.options[this.selectedIndex].value);" name="'. $name .'">'. $data . '</select>';
  }

  function optionMenuPage() {
?>
<script type="text/javascript">
var Teledirwidgets = {
  getCode: function(index) {
    return "<" + "?php if(function_exists('teledirwidgets_display'))teledirwidgets_display("+index+"); ?" + ">";
  },
  change: function(index) {
    var data = '<strong><?php _e('Preview', $this->id); ?></strong> <small><?php _e('(color and border style may change in life view)', $this->id); ?></small><br /><img src="' + this.url + '/screenshot-' + index + '.gif" />';
    if(this.items[index][0] > this.items[index][1]) {
      data += '<div style="color: #ff0000;"><?php _e('Warning! Because of its horizontal dimension, this widget may not be used in the sidebar!', $this->id); ?></div><?php _e('To use this widget place the code below in your template!', $this->id); ?><br /><input type="text" value="'+this.getCode(index)+'" onclick="this.select();" style="width:500px;" /></div>';
    }
    document.getElementById('teledirwidget_preview').innerHTML = data;
    return(false);
  },
  url: '<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/teledirwidgets/',
  items: {
<?php
$widgets = array();
foreach($this->widgets as $k => $v) {
  $widgets[] = sprintf("%d:[%d, %d]", $k, $v['width'], $v['height']);
}
print join(',', $widgets);
?>
}};
</script>
<div class="wrap">
<h2><?=$this->title?></h2>
<div align="center"><p><?=$this->name?> <a href="<?php print( $this->plugin_url ); ?>#teledirwidgets" target="_blank">Plugin Homepage</a> | <a href="http://www.teledir.de/widgets" target="_blank">Widgets Homepage</a></p></div> 
<?php
  if(isset($_POST[$this->id])) {

    $this->updateOptions($_POST[$this->id]);

    echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved!', $this->id) . '</strong></p></div>'; 
  }
?>
<form method="post" action="options-general.php?page=<?=$this->id?>/<?=$this->id?>.php">

<table class="form-table">

<tr valign="top">
  <th scope="row"><?php _e('Title', $this->id); ?></th>
  <td colspan="3"><input name="teledirwidgets[title]" type="text" id="" class="code" value="<?=$this->options['title']?>" /><br /><?php _e('Title is shown above the Sidebar-Widget. If left empty may break your layout in widget mode!', $this->id); ?></td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Background-Color:', $this->id); ?></th>
  <td><input type="text" name="teledirwidgets[background_color]" class="colorpicker" value="<?=$this->options['background_color']?>" maxlength="7" /></td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Border-Color:', $this->id); ?></th>
  <td><input type="text" name="teledirwidgets[border_color]" class="colorpicker" value="<?=$this->options['border_color']?>" maxlength="7" /></td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Font-Color:', $this->id); ?></th>
  <td><input type="text" name="teledirwidgets[font_color]" class="colorpicker" value="<?=$this->options['font_color']?>" maxlength="7" /></td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Border:', $this->id); ?></th>
  <td><input type="radio" name="teledirwidgets[border]" value="0"<?php echo intval($this->options['border']) == 0 ? ' checked="checked"' : ''; ?> /> <?php _e('round', $this->id); ?> <input type="radio" name="teledirwidgets[border]" value="1"<?php echo intval($this->options['border']) == 1 ? ' checked="checked"' : ''; ?> /> <?php _e('edgy', $this->id); ?></td>
</tr>
<tr valign="top">
  <th scope="row"><?php _e('Select widget', $this->id); ?></th>
  <td colspan="3"><?php echo $this->getSelectbox($this->id. '[widget]', $this->widgets, $this->options['widget']); ?><div id="teledirwidget_preview"></div></td>
</tr>

</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('save', $this->id); ?>" class="button" />
</p>
</form>
<script type="text/javascript">
Teledirwidgets.change(<?php echo intval($this->options['widget']); ?>);
</script>
</div>
<?php
  }

  function updateOptions($options) {

    foreach($this->options as $k => $v) {
      if(array_key_exists( $k, $options)) {
        $this->options[ $k ] = trim($options[ $k ]);
      }
    }
        
		update_option($this->id, $this->options);
	}
  
  function loadOptions() {
    $this->options = get_option($this->id);

    if( !$this->options ) {
      $this->options = array(
        'installed' => time(),
        'widget' => 1,
        'border' => 0,
        'border_color' => '#cccccc',
        'background_color' => '#f7f7f7',
        'font_color' => '#000000',
        'title' => 'TeledirWidgets'
			);

      add_option($this->id, $this->options, $this->name, 'yes');
      
      if(is_admin()) {
        add_filter('admin_footer', array(&$this, 'addAdminFooter'));
      }
    }

  }

  function initWidget() {
    if(function_exists('register_sidebar_widget')) {
      register_sidebar_widget($this->title . ' Widget', array($this, 'showWidget'), null, 'widget_'. $this->id);
    }
  }

  function showWidget( $args ) {
    extract($args);
    printf( '%s%s%s%s%s%s', $before_widget, $before_title, $this->options['title'], $after_title, @$this->getCode(null), $after_widget );
  }

  function blogHeader() {
    printf('<meta name="%s" content="%s/%s" />' . "\n", $this->id, $this->id, $this->version);
    print( '<style type="text/css">.teledirwidget2 div, .teledirwidget2 a {text-transform: none !important;font-style: normal !important;letter-spacing: 0 !important;}</style>');
  }

  function getCode($id = null, $border, $border_color, $background_color, $font_color) {

    if(is_null($id)) {
      $id = intval($this->options['widget']);
      $border = intval($this->options['border']);
      $border_color = $this->options['border_color'];
      $background_color = $this->options['background_color'];
      $font_color = $this->options['font_color'];
    }
    
    return str_replace('[PARAMS]', 
    join('&',
      array(
        'type='. $id,
        'border='. $border,
        'font_color'. ( strlen($font_color) == 7)?substr($font_color,1):$font_color,
        'border_color'. ( strlen($border_color) == 7)?substr($border_color,1):$boder_color,
        'background_color'. ( strlen($background_color) == 7)?substr($background_color,1):$background_color
      )
    ),
    $this->widgets[$id]['code']);
  }
}

function teledirwidgets_display($id=null) {

  global $TeledirWidgets;

  if($TeledirWidgets) {
    echo @$TeledirWidgets->getcode($id);
  }
}

add_action('plugins_loaded', create_function( '$TeledirWidgets_5l19o', 'global $TeledirWidgets; $TeledirWidgets = new TeledirWidgets();' ) );

?>