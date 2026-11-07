<?php
defined('ABSPATH') || exit;

final class SND_Accounts {
    private static $error = '';
    private static function posted($key) { return isset($_POST[$key]) && is_string($_POST[$key]) ? wp_unslash($_POST[$key]) : ''; }
    private static function mode() {
        $mode = isset($_GET['mode']) && is_string($_GET['mode']) ? sanitize_key(wp_unslash($_GET['mode'])) : 'login';
        return in_array($mode, array('login','register','lost','reset'), true) ? $mode : 'login';
    }
    private static function limit($action, $suffix = '') {
        $client=SND_Rate::client_id();$window=15*MINUTE_IN_SECONDS;
        if(!SND_Rate::allow('auth:'.$action,$action==='login'?30:12,$window,'client:'.$client,120)){return false;}
        return SND_Rate::allow('auth-identity:'.$action,$action==='login'?8:6,$window,'identity:'.strtolower($suffix),240);
    }
    private static function cookie($value) {
        setcookie('snd_reset_' . COOKIEHASH, $value, array('expires' => $value ? 0 : time() - YEAR_IN_SECONDS, 'path' => wp_parse_url(SND_Site::url('login'), PHP_URL_PATH), 'domain' => COOKIE_DOMAIN ?: '', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax'));
    }
    private static function reset_credentials() {
        $raw = isset($_COOKIE['snd_reset_' . COOKIEHASH]) && is_string($_COOKIE['snd_reset_' . COOKIEHASH]) ? wp_unslash($_COOKIE['snd_reset_' . COOKIEHASH]) : '';
        return strpos($raw, ':') !== false ? explode(':', $raw, 2) : array('', '');
    }
    public static function reset_message($message, $key, $login, $user) {
        $url = add_query_arg(array('mode' => 'reset', 'key' => $key, 'login' => $login), SND_Site::url('login'));
        return SND_Settings::get('app_name')."\n\n".SND_I18n::t('Za nalog {login} zatražena je nova lozinka.',array('login'=>$login))."\n\n".$url."\n\n".SND_I18n::t('Ako nisi zatražio/la promenu, zanemari ovu poruku.')."\n";
    }
    public static function reset_title($title){return SND_Settings::get('app_name').' — '.SND_I18n::t('Nova lozinka');}
    public static function welcome_message($email, $user, $blogname) {
        preg_match_all('~https?://[^\s<>]+~',$email['message'],$matches);$reset='';
        foreach($matches[0] as $link){$parts=array();parse_str(wp_parse_url(html_entity_decode($link),PHP_URL_QUERY)?:'',$parts);if(!empty($parts['key']) && !empty($parts['login'])){$reset=add_query_arg(array('key'=>$parts['key'],'login'=>$parts['login'],'mode'=>'reset'),SND_Site::url('login'));break;}}
        if($reset){$email['message']=SND_Settings::get('app_name')."\n\n".SND_I18n::t('Dobro došao/la, {login}. Postavi lozinku preko ovog linka:',array('login'=>$user->user_login))."\n\n".$reset."\n\n".SND_I18n::t('Prijavi se').': '.SND_Site::url('login')."\n";}
        $email['subject']=str_replace('%','%%',SND_Settings::get('app_name').' — '.SND_I18n::t('Napravi nalog'));
        return $email;
    }
    public static function handle() {
        $view = SND_Site::view();
        if (!in_array($view, array('login','account'), true)) { return; }
        nocache_headers();
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE', true); }
        // Keep same-origin form POSTs identifiable in browsers. Reset keys are
        // removed from the URL before rendering; external sites get no referrer.
        header('Referrer-Policy: same-origin');
        if ($view === 'account') {
            if (!is_user_logged_in()) { wp_safe_redirect(SND_Site::url('login')); exit; }
            return;
        }
        if (!empty($_GET['key']) && !empty($_GET['login']) && is_string($_GET['key']) && is_string($_GET['login'])) {
            $login = wp_unslash($_GET['login']); $key = wp_unslash($_GET['key']);
            $user = check_password_reset_key($key, $login);
            if (!is_wp_error($user)) {
                self::cookie($login . ':' . $key);
                wp_safe_redirect(add_query_arg('mode','reset',SND_Site::url('login'))); exit;
            }
            self::$error = 'Link za promenu lozinke je nevažeći ili je istekao.';
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = self::posted('snd_action');
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if (($origin && wp_parse_url($origin, PHP_URL_HOST) !== wp_parse_url(home_url(), PHP_URL_HOST)) || !wp_verify_nonce(self::posted('_wpnonce'), 'snd_auth_' . $action)) {
                self::$error = 'Forma je istekla. Osveži stranicu i pokušaj ponovo.'; return;
            }
            if ($action === 'logout') {
                SND_Analytics::event('logout',array('view'=>'login'));wp_logout(); wp_safe_redirect(SND_Site::url('home')); exit;
            }
            if (!in_array($action,array('login','register','lost','reset'),true)) { self::$error = 'Nepoznata akcija.'; return; }
            if (!self::limit($action, self::posted('login'))) { self::$error = 'Previše pokušaja. Sačekaj nekoliko minuta i pokušaj ponovo.'; return; }
            if ($action === 'login') {
                $user = wp_signon(array('user_login' => trim(self::posted('login')), 'user_password' => self::posted('password'), 'remember' => (bool) self::posted('remember')), is_ssl());
                if (is_wp_error($user)) { SND_Analytics::event('login_failed',array('view'=>'login','code'=>'invalid_credentials'));self::$error = 'Korisničko ime ili lozinka nisu ispravni.'; return; }
                wp_safe_redirect(SND_Site::url('account')); exit;
            }
            if ($action === 'register') {
                $result = self::register(self::posted('login'), self::posted('email'));
                if (is_wp_error($result)) { self::$error = $result->get_error_message(); return; }
                wp_safe_redirect(add_query_arg('notice','registered',SND_Site::url('login'))); exit;
            }
            if ($action === 'lost') {
                add_filter('retrieve_password_message', array(__CLASS__, 'reset_message'), 10, 4);
                add_filter('retrieve_password_title',array(__CLASS__,'reset_title'));
                retrieve_password(trim(self::posted('login')));
                remove_filter('retrieve_password_message', array(__CLASS__, 'reset_message'), 10);
                remove_filter('retrieve_password_title',array(__CLASS__,'reset_title'));
                wp_safe_redirect(add_query_arg('notice','reset-sent',SND_Site::url('login'))); exit;
            }
            if ($action === 'reset') {
                list($login,$key) = self::reset_credentials(); $user = check_password_reset_key($key,$login);
                $password = self::posted('password'); $errors = new WP_Error();
                if (is_wp_error($user)) { self::$error = 'Link je istekao. Zatraži novi link za lozinku.'; return; }
                if (strlen($password) < 10 || $password !== self::posted('password_confirm')) { self::$error = 'Upiši najmanje 10 znakova i dve iste lozinke.'; return; }
                do_action('validate_password_reset', $errors, $user);
                if ($errors->has_errors()) { self::$error = $errors->get_error_message(); return; }
                reset_password($user, $password); self::cookie('');
                wp_safe_redirect(add_query_arg('notice','password-changed',SND_Site::url('login'))); exit;
            }
        }
        if (is_user_logged_in() && self::mode() !== 'reset') { wp_safe_redirect(SND_Site::url('account')); exit; }
    }
    public static function register($login, $email) {
        if (!get_option('snd_registration_enabled', true)) { return new WP_Error('snd_registration_closed', 'Registracija trenutno nije omogućena.'); }
        $login = sanitize_user(trim($login), true); $email = sanitize_email(trim($email));
        if (!$login || !validate_username($login) || strlen($login) > 60 || !is_email($email)) { return new WP_Error('snd_registration_fields', 'Upiši ispravno korisničko ime i adresu e-pošte.'); }
        if (username_exists($login) || email_exists($email)) { return new WP_Error('snd_registration_exists', 'Ovi podaci su već registrovani. Prijavi se ili zatraži novu lozinku.'); }
        $id = wp_insert_user(array('user_login' => $login, 'user_email' => $email, 'user_pass' => wp_generate_password(32, true, true), 'role' => 'subscriber', 'display_name' => $login));
        if (is_wp_error($id)) { return $id; }
        do_action('snd_player_registered',$id);
        add_filter('wp_new_user_notification_email', array(__CLASS__, 'welcome_message'), 10, 3);
        wp_new_user_notification($id, null, 'user');
        remove_filter('wp_new_user_notification_email', array(__CLASS__, 'welcome_message'), 10);
        return $id;
    }
    public static function form() {
        $mode = self::mode();
        if (($_GET['action'] ?? '') === 'rp') { $mode = 'reset'; }
        $titles = array('login'=>'Prijava','register'=>'Napravi nalog','lost'=>'Zaboravljena lozinka','reset'=>'Nova lozinka');
        $notice = isset($_GET['notice']) && is_string($_GET['notice']) ? sanitize_key($_GET['notice']) : '';
        $messages = array('registered'=>'Proveri e-poštu. Poslali smo link za postavljanje lozinke.', 'reset-sent'=>'Ako nalog postoji, poslat je link za novu lozinku.', 'password-changed'=>'Lozinka je promenjena. Sada možeš da se prijaviš.');
        echo '<section class="snd-auth-card"><h1>' . esc_html($titles[$mode]) . '</h1><p>Sačuvaj rezultate i nastavi igru na drugom uređaju.</p>';
        if (self::$error) { echo '<div class="snd-auth-error" role="alert">' . esc_html(self::$error) . '</div>'; }
        if (isset($messages[$notice])) { echo '<div class="snd-auth-notice" role="status">' . esc_html($messages[$notice]) . '</div>'; }
        echo '<form method="post" action="' . esc_url(add_query_arg('mode',$mode,SND_Site::url('login'))) . '">';
        wp_nonce_field('snd_auth_' . $mode); echo '<input type="hidden" name="snd_action" value="' . esc_attr($mode) . '">';
        if (in_array($mode,array('login','register','lost'),true)) {
            echo '<label for="snd-login">' . ($mode === 'register' ? 'Korisničko ime' : 'Korisničko ime ili e-pošta') . '</label><input id="snd-login" name="login" autocomplete="username" required value="' . esc_attr(self::posted('login')) . '">';
        }
        if ($mode === 'register') { echo '<label for="snd-email">E-pošta</label><input id="snd-email" name="email" type="email" autocomplete="email" value="' . esc_attr(self::posted('email')) . '" required><p class="snd-form-note">Na ovu adresu stiže link za postavljanje lozinke.</p>'; }
        if (in_array($mode,array('login','reset'),true)) {
            echo '<label for="snd-password">Lozinka</label><input id="snd-password" name="password" type="password" autocomplete="' . ($mode === 'login' ? 'current-password' : 'new-password') . '" ' . ($mode === 'reset' ? 'minlength="10"' : '') . ' required>';
        }
        if ($mode === 'reset') { echo '<label for="snd-confirm">Ponovi lozinku</label><input id="snd-confirm" name="password_confirm" type="password" autocomplete="new-password" minlength="10" required>'; }
        if ($mode === 'login') { echo '<label class="snd-checkbox"><input type="checkbox" name="remember" value="1"> Zapamti me</label>'; }
        echo '<button class="snd-app-button" type="submit">' . esc_html(array('login'=>'Prijavi se','register'=>'Napravi nalog','lost'=>'Pošalji link','reset'=>'Sačuvaj lozinku')[$mode]) . '</button></form><div class="snd-auth-links">';
        if ($mode !== 'login') { echo '<a href="' . esc_url(SND_Site::url('login')) . '">Nazad na prijavu</a>'; }
        else {
            if (get_option('snd_registration_enabled', true)) { echo '<a href="' . esc_url(add_query_arg('mode','register',SND_Site::url('login'))) . '">Napravi nalog</a>'; }
            echo '<a href="' . esc_url(add_query_arg('mode','lost',SND_Site::url('login'))) . '">Zaboravljena lozinka?</a>';
        }
        echo '</div></section>';
    }
}
