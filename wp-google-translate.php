<?php
/*
Plugin Name: WP Google Translate
Description: Makes your website multilingual and available to the world using Google Translate.
Version: 1.0.0
Author: 2glux

*/

add_action('widgets_init', array('GoogleTranslate', 'register'));
register_activation_hook(__FILE__, array('GoogleTranslate', 'activate'));
register_deactivation_hook(__FILE__, array('GoogleTranslate', 'deactivate'));
add_action('admin_menu', array('GoogleTranslate', 'admin_menu'));
add_shortcode('google-translate', array('GoogleTranslate', 'widget_code'));

class GoogleTranslate extends WP_Widget {
    function activate() {
        $data = array('google-translate_title' => 'Google Translate',);
        $data = get_option('google-translate');
        GoogleTranslate::load_defaults($data);

        add_option('google-translate', $data);
    }

    function deactivate() {}

    function control() {
        $data = get_option('google-translate');
        ?>
        <p><label>Title: <input name="google-translate_title" type="text" class="widefat" value="<?php echo $data['google-translate_title']; ?>"/></label></p>
        <?php
        if (isset($_POST['google-translate_title'])){
            $data['google-translate_title'] = attribute_escape($_POST['google-translate_title']);
            update_option('google-translate', $data);
        }
    }

    function widget($args) {
        $data = get_option('google-translate');
        GoogleTranslate::load_defaults($data);

        if(empty($data['google-translate_title']))
            $data['google-translate_title'] = 'Google Translate';

        echo $args['before_widget'];
        echo $args['before_title'] . $data['google-translate_title'] . $args['after_title'];
        echo self::widget_code();
        echo $args['after_widget'];
    }

    function widget_code($atts = array()) {
        $data = get_option('google-translate');
        GoogleTranslate::load_defaults($data);
        $mixed_language = $data['mixed_language'] ? 'true' : 'false';

        $site_url = site_url();
        $plugin_dir = plugins_url( '', __FILE__ );

        $script = <<< EOM
<style type="text/css">
<!--
a.gflag {vertical-align:middle;font-size:16px;padding:1px 0;background-repeat:no-repeat;background-image:url('{$plugin_dir}/16.png');}
a.gflag img {border:0;}
a.gflag:hover {background-image:url('{$plugin_dir}/16a.png');}
#goog-gt-tt {display:none !important;}
.goog-te-banner-frame {display:none !important;}
.goog-te-menu-value:hover {text-decoration:none !important;}
body {top:0 !important;}
#google_translate_element2 {display:none!important;}
-->
</style>

<a href="#" onclick="doGoogleTranslate('en|en');return false;" title="English" class="gflag nturl" style="background-position:-0px -0px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="English" /></a><a href="#" onclick="doGoogleTranslate('en|fr');return false;" title="French" class="gflag nturl" style="background-position:-200px -100px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="French" /></a><a href="#" onclick="doGoogleTranslate('en|de');return false;" title="German" class="gflag nturl" style="background-position:-300px -100px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="German" /></a><a href="#" onclick="doGoogleTranslate('en|it');return false;" title="Italian" class="gflag nturl" style="background-position:-600px -100px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="Italian" /></a><a href="#" onclick="doGoogleTranslate('en|pt');return false;" title="Portuguese" class="gflag nturl" style="background-position:-300px -200px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="Portuguese" /></a><a href="#" onclick="doGoogleTranslate('en|ru');return false;" title="Russian" class="gflag nturl" style="background-position:-500px -200px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="Russian" /></a><a href="#" onclick="doGoogleTranslate('en|es');return false;" title="Spanish" class="gflag nturl" style="background-position:-600px -200px;"><img src="{$plugin_dir}/blank.png" height="16" width="16" alt="Spanish" /></a>
<br />
<select onchange="doGoogleTranslate(this);"><option value="">Select Language</option><option value="en|af">Afrikaans</option><option value="en|sq">Albanian</option><option value="en|ar">Arabic</option><option value="en|hy">Armenian</option><option value="en|az">Azerbaijani</option><option value="en|eu">Basque</option><option value="en|be">Belarusian</option><option value="en|bg">Bulgarian</option><option value="en|ca">Catalan</option><option value="en|zh-CN">Chinese (Simplified)</option><option value="en|zh-TW">Chinese (Traditional)</option><option value="en|hr">Croatian</option><option value="en|cs">Czech</option><option value="en|da">Danish</option><option value="en|nl">Dutch</option><option value="en|en">English</option><option value="en|et">Estonian</option><option value="en|tl">Filipino</option><option value="en|fi">Finnish</option><option value="en|fr">French</option><option value="en|gl">Galician</option><option value="en|ka">Georgian</option><option value="en|de">German</option><option value="en|el">Greek</option><option value="en|ht">Haitian Creole</option><option value="en|iw">Hebrew</option><option value="en|hi">Hindi</option><option value="en|hu">Hungarian</option><option value="en|is">Icelandic</option><option value="en|id">Indonesian</option><option value="en|ga">Irish</option><option value="en|it">Italian</option><option value="en|ja">Japanese</option><option value="en|ko">Korean</option><option value="en|lv">Latvian</option><option value="en|lt">Lithuanian</option><option value="en|mk">Macedonian</option><option value="en|ms">Malay</option><option value="en|mt">Maltese</option><option value="en|no">Norwegian</option><option value="en|fa">Persian</option><option value="en|pl">Polish</option><option value="en|pt">Portuguese</option><option value="en|ro">Romanian</option><option value="en|ru">Russian</option><option value="en|sr">Serbian</option><option value="en|sk">Slovak</option><option value="en|sl">Slovenian</option><option value="en|es">Spanish</option><option value="en|sw">Swahili</option><option value="en|sv">Swedish</option><option value="en|th">Thai</option><option value="en|tr">Turkish</option><option value="en|uk">Ukrainian</option><option value="en|ur">Urdu</option><option value="en|vi">Vietnamese</option><option value="en|cy">Welsh</option><option value="en|yi">Yiddish</option></select>

<div id="google_translate_element2"></div>

<script type="text/javascript">
function googleTranslateElementInit2() {new google.translate.TranslateElement({pageLanguage: '{$data[default_language]}',autoDisplay: false,multilanguagePage: $mixed_language}, 'google_translate_element2');}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>

<script type="text/javascript">
function GoogleTranslateFireEvent(element, event) {try {if (document.createEventObject){var evt = document.createEventObject();element.fireEvent('on'+event,evt);} else {var evt = document.createEvent("HTMLEvents");evt.initEvent(event, true, true );element.dispatchEvent(evt);}} catch (e) {}}
function doGoogleTranslate(lang_pair) {
    if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];

    var teCombo;
    var sel = document.getElementsByTagName('select');
    for(var i = 0; i < sel.length; i++)
       if(sel[i].className == 'goog-te-combo')
           teCombo = sel[i];

    if(document.getElementById('google_translate_element2') == null || document.getElementById('google_translate_element2').innerHTML.length == 0 || teCombo.length == 0  || teCombo.innerHTML.length == 0) {setTimeout(function() { doGoogleTranslate(lang_pair); }, 500);}
    else {
        teCombo.value = lang;
        GoogleTranslateFireEvent(teCombo,'change');GoogleTranslateFireEvent(teCombo,'change');
    }
}
</script>

EOM;

        return $script;
    }

    function register() {
        wp_register_sidebar_widget('google-translate', 'WP Google Translate', array('GoogleTranslate', 'widget'), array('description' => __('Google Translate Widget')));
        wp_register_widget_control('google-translate', 'WP Google Translate', array('GoogleTranslate', 'control'));
    }

    function admin_menu() {
        add_options_page('google-translate options', 'WP Google Translate', 'administrator', 'google-translate_options', array('GoogleTranslate', 'options'));
    }

    function options() {
        ?>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"><br/></div>
        <h2>Google Translate</h2>
        <?php
        if($_POST['save'])
            GoogleTranslate::control_options();
        $data = get_option('google-translate');
        GoogleTranslate::load_defaults($data);

        $site_url = site_url();

        extract($data);

?>
        <form id="google-translate" name="form1" method="post" action="<?php echo admin_url() . '/options-general.php?page=google-translate_options' ?>">
        <h4>Widget options</h4>
        <table style="font-size:11px;">
        <tr>
            <td class="option_name">Default language:</td>
            <td>
                <select id="default_language" name="default_language">
                    <option value="auto" <?php if($data['default_language'] == 'auto') echo 'selected'; ?>>Detect language</option>
                    <option value="af" <?php if($data['default_language'] == 'af') echo 'selected'; ?>>Afrikaans</option>
                    <option value="sq" <?php if($data['default_language'] == 'sq') echo 'selected'; ?>>Albanian</option>
                    <option value="ar" <?php if($data['default_language'] == 'ar') echo 'selected'; ?>>Arabic</option>
                    <option value="hy" <?php if($data['default_language'] == 'hy') echo 'selected'; ?>>Armenian</option>
                    <option value="az" <?php if($data['default_language'] == 'az') echo 'selected'; ?>>Azerbaijani</option>
                    <option value="eu" <?php if($data['default_language'] == 'eu') echo 'selected'; ?>>Basque</option>
                    <option value="be" <?php if($data['default_language'] == 'be') echo 'selected'; ?>>Belarusian</option>
                    <option value="bg" <?php if($data['default_language'] == 'bg') echo 'selected'; ?>>Bulgarian</option>
                    <option value="ca" <?php if($data['default_language'] == 'ca') echo 'selected'; ?>>Catalan</option>
                    <option value="zh-CN" <?php if($data['default_language'] == 'zh-CN') echo 'selected'; ?>>Chinese (Simplified)</option>
                    <option value="zh-TW" <?php if($data['default_language'] == 'zh-TW') echo 'selected'; ?>>Chinese (Traditional)</option>
                    <option value="hr" <?php if($data['default_language'] == 'hr') echo 'selected'; ?>>Croatian</option>
                    <option value="cs" <?php if($data['default_language'] == 'cs') echo 'selected'; ?>>Czech</option>
                    <option value="da" <?php if($data['default_language'] == 'da') echo 'selected'; ?>>Danish</option>
                    <option value="nl" <?php if($data['default_language'] == 'nl') echo 'selected'; ?>>Dutch</option>
                    <option value="en" <?php if($data['default_language'] == 'en') echo 'selected'; ?>>English</option>
                    <option value="et" <?php if($data['default_language'] == 'et') echo 'selected'; ?>>Estonian</option>
                    <option value="tl" <?php if($data['default_language'] == 'tl') echo 'selected'; ?>>Filipino</option>
                    <option value="fi" <?php if($data['default_language'] == 'fi') echo 'selected'; ?>>Finnish</option>
                    <option value="fr" <?php if($data['default_language'] == 'fr') echo 'selected'; ?>>French</option>
                    <option value="gl" <?php if($data['default_language'] == 'gl') echo 'selected'; ?>>Galician</option>
                    <option value="ka" <?php if($data['default_language'] == 'ka') echo 'selected'; ?>>Georgian</option>
                    <option value="de" <?php if($data['default_language'] == 'de') echo 'selected'; ?>>German</option>
                    <option value="el" <?php if($data['default_language'] == 'el') echo 'selected'; ?>>Greek</option>
                    <option value="ht" <?php if($data['default_language'] == 'ht') echo 'selected'; ?>>Haitian Creole</option>
                    <option value="iw" <?php if($data['default_language'] == 'iw') echo 'selected'; ?>>Hebrew</option>
                    <option value="hi" <?php if($data['default_language'] == 'hi') echo 'selected'; ?>>Hindi</option>
                    <option value="hu" <?php if($data['default_language'] == 'hu') echo 'selected'; ?>>Hungarian</option>
                    <option value="is" <?php if($data['default_language'] == 'is') echo 'selected'; ?>>Icelandic</option>
                    <option value="id" <?php if($data['default_language'] == 'id') echo 'selected'; ?>>Indonesian</option>
                    <option value="ga" <?php if($data['default_language'] == 'ga') echo 'selected'; ?>>Irish</option>
                    <option value="it" <?php if($data['default_language'] == 'it') echo 'selected'; ?>>Italian</option>
                    <option value="ja" <?php if($data['default_language'] == 'ja') echo 'selected'; ?>>Japanese</option>
                    <option value="ko" <?php if($data['default_language'] == 'ko') echo 'selected'; ?>>Korean</option>
                    <option value="lv" <?php if($data['default_language'] == 'lv') echo 'selected'; ?>>Latvian</option>
                    <option value="lt" <?php if($data['default_language'] == 'lt') echo 'selected'; ?>>Lithuanian</option>
                    <option value="mk" <?php if($data['default_language'] == 'mk') echo 'selected'; ?>>Macedonian</option>
                    <option value="ms" <?php if($data['default_language'] == 'ms') echo 'selected'; ?>>Malay</option>
                    <option value="mt" <?php if($data['default_language'] == 'mt') echo 'selected'; ?>>Maltese</option>
                    <option value="no" <?php if($data['default_language'] == 'no') echo 'selected'; ?>>Norwegian</option>
                    <option value="fa" <?php if($data['default_language'] == 'fa') echo 'selected'; ?>>Persian</option>
                    <option value="pl" <?php if($data['default_language'] == 'pl') echo 'selected'; ?>>Polish</option>
                    <option value="pt" <?php if($data['default_language'] == 'pt') echo 'selected'; ?>>Portuguese</option>
                    <option value="ro" <?php if($data['default_language'] == 'ro') echo 'selected'; ?>>Romanian</option>
                    <option value="ru" <?php if($data['default_language'] == 'ru') echo 'selected'; ?>>Russian</option>
                    <option value="sr" <?php if($data['default_language'] == 'sr') echo 'selected'; ?>>Serbian</option>
                    <option value="sk" <?php if($data['default_language'] == 'sk') echo 'selected'; ?>>Slovak</option>
                    <option value="sl" <?php if($data['default_language'] == 'sl') echo 'selected'; ?>>Slovenian</option>
                    <option value="es" <?php if($data['default_language'] == 'es') echo 'selected'; ?>>Spanish</option>
                    <option value="sw" <?php if($data['default_language'] == 'sw') echo 'selected'; ?>>Swahili</option>
                    <option value="sv" <?php if($data['default_language'] == 'sv') echo 'selected'; ?>>Swedish</option>
                    <option value="th" <?php if($data['default_language'] == 'th') echo 'selected'; ?>>Thai</option>
                    <option value="tr" <?php if($data['default_language'] == 'tr') echo 'selected'; ?>>Turkish</option>
                    <option value="uk" <?php if($data['default_language'] == 'uk') echo 'selected'; ?>>Ukrainian</option>
                    <option value="ur" <?php if($data['default_language'] == 'ur') echo 'selected'; ?>>Urdu</option>
                    <option value="vi" <?php if($data['default_language'] == 'vi') echo 'selected'; ?>>Vietnamese</option>
                    <option value="cy" <?php if($data['default_language'] == 'cy') echo 'selected'; ?>>Welsh</option>
                    <option value="yi" <?php if($data['default_language'] == 'yi') echo 'selected'; ?>>Yiddish</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="option_name">Mixed language content:</td>
            <td><input id="mixed_language" name="mixed_language" value="1" type="checkbox" <?php if($data['mixed_language']) echo 'checked'; ?> /></td>
        </tr>
        </table>

        <?php wp_nonce_field('google-translate-save'); ?>
        <p class="submit"><input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes'); ?>" /></p>
        </form>
        </div>
        <?php
    }

    function control_options() {
        check_admin_referer('google-translate-save');

        $data = get_option('google-translate');

        $data['mixed_language'] = isset($_POST['mixed_language']) ? $_POST['mixed_language'] : '';
        $data['default_language'] = $_POST['default_language'];
        $data['incl_langs'] = $_POST['incl_langs'];

        echo '<p style="color:red;">Changes Saved</p>';
        update_option('google-translate', $data);
    }

    function load_defaults(& $data) {
        $data['mixed_language'] = isset($data['mixed_language']) ? $data['mixed_language'] : '';
        $data['default_language'] = isset($data['default_language']) ? $data['default_language'] : 'en';
        $data['incl_langs'] = isset($data['incl_langs']) ? $data['incl_langs'] : array();
    }
}
