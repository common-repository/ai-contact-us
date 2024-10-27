<?php
/*
Plugin Name: AI Contact Us
Plugin URI: https://wordpress.org/plugins/ai-contact-us/
Description: Contact Us
Version: 1.0
Author: Karishma Arora
Author URI: http://ec2-13-127-115-21.ap-south-1.compute.amazonaws.com/index.php/contact-us
License: GPLv2
*/

add_action('admin_menu', 'qdcu_create_menu');
function qdcu_create_menu() {

    add_menu_page('Edit AI Contact Form', 'AI Contact Form', 'administrator', 'qdcontactus', 'qdcu_settings_page' , 'dashicons-email' );
    add_action('admin_init', 'register_qdcu_settings');
}
function register_qdcu_settings() {
    register_setting('cu-settings-email-group', 'qdcu_to_email');
    register_setting('cu-settings-email-group', 'qdcu_success_message');
    register_setting('cu-settings-email-group', 'qdcu_error_message');
    register_setting('cu-settings-theme-group', 'qdcu_theme');
}
function qdcu_settings_page() {
    ?>
    <div class="wrap">
        <h1>Edit AI Contact Form</h1>

        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=qdcontactus') ?>" class="nav-tab <?php echo $_GET['tab']==''||$_GET['tab']=='general'?'nav-tab-active':''?>">General</a>
            <a href="<?php echo admin_url('admin.php?page=qdcontactus&tab=email') ?>" class="nav-tab <?php echo $_GET['tab']=='email'?'nav-tab-active':''?>">Email</a>
            <a href="<?php echo admin_url('admin.php?page=qdcontactus&tab=theme') ?>" class="nav-tab <?php echo $_GET['tab']=='theme'?'nav-tab-active':''?>">Theme</a>
        </nav>
        <form method="post" action="<?php echo admin_url('options.php') ?>">

        <?php if($_GET['tab'] == '' || $_GET['tab'] == 'general'){ ?>
                <?php settings_fields( 'cu-settings-general-group' ); ?>
                <?php do_settings_sections( 'cu-settings-general-group' ); ?>

                <h3>General Settings</h3>
                <?php submit_button(); ?>
        <?php } ?>
        <?php if($_GET['tab'] == 'email'){ ?>
                <?php settings_fields( 'cu-settings-email-group' ); ?>
                <?php do_settings_sections( 'cu-settings-email-group' ); ?>
                <h3>Email Settings</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">To Email</th>
                        <td><input type="email" name="qdcu_to_email" class="code regular-text" value="<?php echo esc_attr( get_option('qdcu_to_email') ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Sender's message was sent successfully</th>
                        <td><input type="text" name="qdcu_success_message" class="code regular-text" value="<?php echo esc_attr( get_option('qdcu_success_message') ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Sender's message failed to send</th>
                        <td><input type="text" name="qdcu_error_message" class="code regular-text" value="<?php echo esc_attr( get_option('qdcu_error_message') ); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
        <?php } ?>
        <?php if($_GET['tab'] == 'theme'){ ?>
                <?php settings_fields( 'cu-settings-theme-group' ); ?>
                <?php do_settings_sections( 'cu-settings-theme-group' ); ?>
                <h3>Theme Settings</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Themes</th>
                        <td>
                            <select id="qdcu_theme" name="qdcu_theme" class="code small-text">
                                <option value="light" <?php echo get_option('qdcu_theme') == 'light'?'selected':''?>>Light</option>
                                <option value="dark" <?php echo get_option('qdcu_theme') == 'dark'?'selected':''?>>Dark</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
        <?php } ?>
        </form>
    </div>
<?php }


/*add_action('admin_init', 'qdcu_register_settings');
function qdcu_register_settings() {
    add_option( 'qdcu_option_name', 'This is my option value.');
    register_setting( 'qdcu_options_group', 'qdcu_option_name', 'qdcu_callback' );
}*/

/*add_action('admin_menu', 'qdcu_register_options_page');
function qdcu_register_options_page() {
    add_options_page('Contact Us', 'Contact Us', 'manage_options', 'cu', 'qdcu_options_page');
}*/

add_action('init', 'qdcu_init_contact_us');
function qdcu_init_contact_us() {
    add_shortcode('contact_us', 'qdcu_contact_us_form');
    add_action('wp_head', 'qdcu_add_resources');
}

function qdcu_add_resources() {
    ?>
    <?php wp_enqueue_style('contactus', ''.plugin_dir_url(__FILE__).'css/contactus.css', array(), false, 'all' )?>
    <?php wp_enqueue_script('conversational-form', 'https://cdn.jsdelivr.net/gh/space10-community/conversational-form@0.9.80/dist/conversational-form.min.js', array( 'jquery' ), false, 1);?>
    <?php wp_enqueue_script('contactus', ''.plugin_dir_url(__FILE__).'js/contactus.js', array( 'jquery' ), false, 1);?>
    <?php
    $dataToBePassed = array(
    'contactus_ajaxurl' => site_url('wp-admin/admin-ajax.php'),
    'plugin_url'        => plugin_dir_url(__FILE__),
    'success_message'   => get_option('qdcu_success_message'),
    'error_message'     => get_option('qdcu_error_message')
    );
    wp_localize_script( 'contactus', 'php_vars', $dataToBePassed );?>
    <?php
}
function qdcu_contact_us_form() {
    ?>
    <main id="conversational-form-docs" class="content <?php echo get_option('qdcu_theme')?>-theme">
        <section id="info" role="info">
            <article>
                <h1 id="writer">
                </h1>
            </article>
        </section>
        <section id="form" role="form">
            <form id="cf-form" method="post" action="/contact-us">
                <fieldset>
                    <label for="name">What's your name?</label>
                    <input required cf-questions="Hi there! What's your name? 😊" type="text" class="form-control" name="name" id="name">
                </fieldset>
                <fieldset>
                    <label for="email">What's your email</label>
                    <input pattern=".+\@.+\..+" cf-error="E-mail not correct" cf-questions="What's your email address?" type="email" class="form-control" name="email" id="your-email">
                </fieldset>
                <fieldset>
                    <label for="subject">Subject</label>
                    <input cf-questions="On which topic you want to contact with us,leave the summary.." type="text" class="form-control" name="subject" id="subject">
                </fieldset>
                <fieldset>
                    <label for="message">Message</label>
                    <input cf-questions="Leave your message,here!!" type="text" class="form-control" name="message" id="message">
                </fieldset>

                <fieldset style="display: none;">
                    <label for="thats-all">Are you ready to submit the form?</label>
                    <select cf-questions="Are you ready to submit the form?" name="submit-form" id="submit-form" class="form-control">
                        <option></option>
                        <option>Yup</option>
                    </select>
                </fieldset>

            </form>
        </section>
        <section id="cf-context" role="cf-context" cf-context>

        </section>
    </main>
    <?php
}

add_action('wp_ajax_qdcu_send_email', 'qdcu_send_email');
add_action('wp_ajax_nopriv_qdcu_send_email', 'qdcu_send_email');

function qdcu_send_email() {

    if( isset( $_POST['submit-form'] ) ) {
        global $errors;

        if (empty($errors->errors)) {
            $html = "";
            $name = qdcu_sanitize_field($_POST['name']);
            $email = sanitize_email($_POST['email']);
            $email_subject = qdcu_sanitize_field($_POST['subject']);
            $email_message = qdcu_sanitize_field($_POST['message']);
            $to_email = get_option('qdcu_to_email');
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = "Reply-To: $email \r\n";

            $html .= "<div style='background:#0e6b77;padding:20px;font-family:Arial, Helvetica, sans-serif;font-size:13px;'>";
            $html .= "<table align='center' cellpadding='0' cellspacing='10' width='700' bgcolor='#ffffff' style='color: #000000;'>";
            $html .= "<tr><td align='center'>" . get_bloginfo('name') . "</td></tr>";
            $html .= "<tr><td>Dear Admin,<br /><p>" . $name . " has been wanted to contact you, along with following information:</p></td></tr>";
            $html .= "<tr><td>Name: " . $name . "<br />Email: " . $email . "<br />Message: " . $email_message . "</td></tr>";

            $html .= "</table>";
            $html .= "</div>";

            echo wp_mail($to_email, $email_subject, $html, $headers);
        }
        else {
            echo $errors;
        }
        exit();
    }
}

function qdcu_validate_form() {

    $errors = new WP_Error();

    if ( isset( $_POST[ 'name' ] ) && $_POST[ 'name' ] == '' ) {
        $errors->add('name_error', 'Please fill in a valid name.' );
    }

    if ( isset( $_POST[ 'email' ] ) && $_POST[ 'email' ] == '' ) {
        $errors->add('email_error', 'Please fill in a valid email.' );
    }
    if ( isset( $_POST[ 'subject' ] ) && $_POST[ 'subject' ] == '' ) {
        $errors->add('subject_error', 'Please fill in a valid subject.' );
    }
    if ( isset( $_POST[ 'message' ] ) && $_POST[ 'message' ] == '' ) {
        $errors->add('message_error', 'Please fill in a valid message.' );
    }

    return $errors;
}

function qdcu_sanitize_field( $input ){

    return trim( stripslashes( sanitize_text_field ( $input ) ) );

}
