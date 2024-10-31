<?php
if (!class_exists('POUCOImportUsers')) {
    class POUCOImportUsers
    {

        /**
          * Textdomain used for translation & for created custom post type. Use the set_textdomain() method to set a custom textdomain.
          * @author Morgan JOURDIN
          * @var string $textdomain
          */
        private static $textdomain = 'poucoimportusers';

        /**
          * Number of total users (update/new)
          * @author Morgan JOURDIN
          * @var int $totalUser
          */
        private $totalUsers = 0;

        /**
          * Number of total new users created
          * @author Morgan JOURDIN
          * @var int $totalNewUser
          */
        private $totalNewUsers = 0;

        /**
          * Number of total users updated
          * @author Morgan JOURDIN
          * @var int $allUpdateUser
          */
        private $totalUpdateUsers = 0;

        /**
          * Array of new users
          * @author Morgan JOURDIN
          * @var int $arrayNewUsers
          */
        private static $arrayNewUsers = array();

        /**
          * Array of updated users
          * @author Morgan JOURDIN
          * @var int $arrayUpdatedUsers
          */
        private static $arrayUpdatedUsers = array();

        /**
          * Global used to init class with singleton function
          * @author Morgan JOURDIN
          * @var null
          */
        private static $_instance = null;

        /**
          * Constructor
          * @author Morgan JOURDIN
          * @return null
          */
        public function __construct()
        {
            // Load up the localization file if we're using WordPress in a different language
            load_plugin_textdomain(self::$textdomain, false, dirname(plugin_basename(__FILE__)) . '/languages/');

            //enable menu BO
            add_action('admin_menu', array( &$this, 'add_admin_menu' ));

            //Add SCRIPT & CSS
            add_action('admin_enqueue_scripts', array( &$this, 'register_script' ));

            //Ajax
            add_action('wp_ajax_fileUpload', array( &$this, 'fileUpload' ));
            add_action('wp_ajax_nopriv_fileUpload', array( &$this, 'fileUpload' ));
            add_action('wp_ajax_users_create', array( &$this, 'users_create' ));
            add_action('wp_ajax_nopriv_users_create', array( &$this, 'users_create' ));
            add_action('wp_ajax_users_update', array( &$this, 'users_update' ));
            add_action('wp_ajax_nopriv_users_update', array( &$this, 'users_update' ));
        }

        /**
          * Display error with design
          * @author Morgan JOURDIN
          * @param  array $tab
          * @return HTML  <pre></pre>
          */
        public function _error_($tab)
        {
            return '<pre>'.print_r($tab, true).'</pre>';
        }


        /**
          *
          * Singleton
          * @author Morgan JOURDIN
          * @return instance class
          */
        public static function get_instance()
        {
            if (self::$_instance === null) {
                self::$_instance = new MJImportUsers;
            }

            return self::$_instance;
        }


        /**
          *
          * To the activation of the plugin
          * @author Morgan JOURDIN
          * @return null
          */
        public function plugin_activation()
        {
            if (version_compare($GLOBALS['wp_version'], POUCOIMPORTUSERS_MINIMUM_WP_VERSION, '<')) {
                $message = '<strong>' . sprintf(esc_html__('POUCO Import Users %s requires WordPress %s or higher.', self::$textdomain), POUCOIMPORTUSERS_VERSION, POUCOIMPORTUSERS_MINIMUM_WP_VERSION). '</strong>';
            }
        }


        /**
          *
          * To the desactivation of the plugin
          * @author Morgan JOURDIN
          * @return null
          */
        public function plugin_desactivation()
        {
            $target = WP_CONTENT_DIR . '/uploads/' . self::$textdomain . '/';
            if (is_dir($target)) {
                $mask = $target . '*.csv';
                array_map("unlink", glob($mask)); //delete all file

                rmdir($target); //delete folder
            }
        }


        /**
          * Add sub-menu to users menu
          * @author Morgan JOURDIN
          * @return null
          */
        public function add_admin_menu()
        {
            $this->menu_id = add_submenu_page(
                'users.php',
                __('Import', 'poucoimportusers'),
                __('Import', 'poucoimportusers'),
                'manage_options',
                'import',
                array($this, 'dashboard_admin')
            );
        }

        /**
          *
          * Function register_script
          * @author Morgan JOURDIN
          * @return null
          */
        public function register_script($hook_suffix)
        {
            //CSS
            wp_register_style('fontawesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css', array(), '', false);
            wp_register_style('poucoimportusers', plugins_url('public/css/poucoimportusers.min.css', __FILE__), array(), '', false);

            //scripts
            wp_register_script('poucoimportusers-core', plugins_url('public/js/poucoimportusers-core.js', __FILE__), array(), POUCOIMPORTUSERS_VERSION, true);
            wp_register_script('poucoimportusers-update', plugins_url('public/js/poucoimportusers-update.js', __FILE__), array(), POUCOIMPORTUSERS_VERSION, true);
            wp_register_script('poucoimportusers-create', plugins_url('public/js/poucoimportusers-create.js', __FILE__), array(), POUCOIMPORTUSERS_VERSION, true);
            wp_register_script('poucoimportusers-download', plugins_url('public/js/poucoimportusers-download.js', __FILE__), array(), POUCOIMPORTUSERS_VERSION, true);
            wp_localize_script('poucoimportusers-core', 'ajax_url', admin_url('admin-ajax.php'));
            wp_localize_script(
                'poucoimportusers-core',
                'errors',
              array(
                5000 => __('Error 5000: There is an error with the server. Please contact the webmaster: hi@pouco.ooo', 'poucoimportusers'),
                5001 => __('Error 5001: There is an error with the server for update the user. Please contact the webmaster: hi@pouco.ooo', 'poucoimportusers'),
                5002 => __('Error 5002: There is an error with the server for create the user. Please contact the webmaster: hi@pouco.ooo', 'poucoimportusers'),
                4033 => __('Error 4033: Too bad ! Not CSV file. Try your luck again...', 'poucoimportusers'),
                4034 => __('Error 4034: A problem occurred when loading the csv file. Please contact the webmaster: hi@pouco.ooo', 'poucoimportusers'),
                4035 => __('Error 4035: A problem occurred when creating the user profil ->', 'poucoimportusers'),
                4036 => __('Error 4036: A problem occurred when updating the user profil ->', 'poucoimportusers'),
                4037 => __('Error 4037: User not update ->', 'poucoimportusers'),
                4039 => __('Error 4039: CSV file required <b>User Login</b> and <b>User Email</b> for create new user ->', 'poucoimportusers'),
                4042 => __('Error 4039: CSV file required <b>User Login</b> and <b>User Email</b> for update user ->', 'poucoimportusers'),
                4040 => __('Error 4040: There were one or more errors during the user update', 'poucoimportusers'),
                4041 => __('Error 4041: There were one or more errors during the user create', 'poucoimportusers')
              )
            );

            wp_localize_script(
                'poucoimportusers-core',
                'success',
              array(
                2000 => __('Success: The upload is complete', 'poucoimportusers'),
                2001 => __('Success: User creation is complete', 'poucoimportusers'),
                2002 => __('Success: User update is complete', 'poucoimportusers')
              )
            );

            wp_localize_script(
                'poucoimportusers-core',
                'other',
              array(
                "see" => __('See the details', 'poucoimportusers')
              )
            );
        }

        /**
          * Display the content in admin page import users
          * @author Morgan JOURDIN
          * @return null
          */
        public function dashboard_admin()
        {
            $html = '';
            global $wpdb;

            //Load styles
            wp_enqueue_style('fontawesome');
            wp_enqueue_style('poucoimportusers');

            //Load scripts
            wp_enqueue_script('poucoimportusers-core');
            wp_enqueue_script('poucoimportusers-download');
            wp_enqueue_script('poucoimportusers-update');
            wp_enqueue_script('poucoimportusers-create');

            $html .= '<script type="text/javascript">(function(t,e){var r=function(t){try{var r=e.head||e.getElementsByTagName("head")[0];a=e.createElement("script");a.setAttribute("type","text/javascript");a.setAttribute("src",t);r.appendChild(a);}catch(t){}};t.CollectId = "5c3c98f3a5f8230a0058d662";r("https://collectcdn.com/launcher.js");})(window,document);</script>';

            //En-tête
            $html .= '<div class="wrap poucoimportusers">';

            $html .= '
              <div class="errorPopup" style="display: none;">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-12 col-lg-6 col-md-8">
                      <div class="content">
                        <i class="fas fa-times quit"></i>
                        <div class="limit">
                          <span class="title">' . __('Error console', 'poucoimportusers') . '</span>
                          <code></code>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ';

            $html .= '<div class="container-fluid">';
            $html .= '<div class="row">';

            $html .= '<div class="left col-12">';
            $html .= '<div class="row">';

            $html .= '<div class="head col-12">';
            $html .= '<h2><img width="200px" src="' . plugins_url('/public/img/pouco-import_users-logo.png', __FILE__) . '" title="Pouco Import Users"/></h2>';
            $html .= '</div>';
            /*-------------------------------*/

            //Main
            $html .= '<div class="main col-12">';
            $html .= '<div class="row">';
            $html .= '<div class="col-12 col-lg-6 col-md-6 calltoaction">';

            $html .= '
              <form method="post" action="" id="fileUploadForm" enctype="multipart/form-data">
                <span class="btn btn-success fileinput-button">
                  <i class="fas fa-plus"></i>
                  <span>' . __('Select CSV files...', 'poucoimportusers') . '</span>
                  <!-- The file input field used as target for the file upload widget -->
                  <input id="fileupload" type="file" name="files">
                </span>
              </form>
              <div class="boxbar">
                <h4>' . __('Download', 'poucoimportusers') . '</h4>
                <div class="limitbar">
                  <span id="cursordownload" class="cursor" data-purcent="0%" style="left:0%;"></span>
                  <div id="progressdownload" class="progressbar">
                      <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                  </div>
                </div>
                <div class="errors" id="errorDownload"></div>
                <div class="success" id="successDownload"></div>
              </div>
              <div class="boxbar">
                <h4>' . __('Create Users', 'poucoimportusers') . '</h4>
                <div class="limitbar">
                  <span id="cursorcreate" class="cursor" data-purcent="0%" style="left:0%;"></span>
                  <div id="progresscreate" class="progressbar">
                      <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                  </div>
                </div>
                <div class="errors" id="errorcreate"></div>
                <div class="success" id="successcreate"></div>
              </div>
              <div class="boxbar">
                <h4>' . __('Update Users', 'poucoimportusers') . '</h4>
                <div class="limitbar">
                  <span id="cursorupdate" class="cursor" data-purcent="0%" style="left:0%;"></span>
                  <div id="progressupdate" class="progressbar">
                      <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                  </div>
                </div>
                <div class="errors" id="errorupdate"></div>
                <div class="success" id="successupdate"></div>
              </div>
            ';
            /*-------------------------------*/
            $html .= '</div>';

            $html .= '<div class="col-12 col-lg-6 col-md-6 infos">';
            $html .= '
              <div class="boxbar">
                <h2><i class="fas fa-coffee"></i>' . __('Instructions', 'poucoimportusers') . '</h2>
                <p>' . __('Click on <b>Select Files</b> for download the users CSV file in WordPress.<br />Once the download is complete, users will be immediately imported into the database.<br />If an user already exists he will be updated in the database else he will be created.<br /><b>User login</b> and <b>User e-mail</b> is required.<br />You will be able to observe the progress in real time.', 'poucoimportusers') . '</p>
              </div>
            ';
            $html .= '
              <div class="boxbar">
                <h2><i class="fas fa-download"></i>' . __('CSV', 'poucoimportusers') . '</h2>
                <p>' . __('You can download a sample file correctly formatted: <a href="' . plugins_url('/public/example/example.csv', __FILE__) . '">example.csv</a>', 'poucoimportusers') . '</p>
                <img src="' . plugins_url('/public/img/capture.png', __FILE__) . ' "width="100%" />
              </div>
            ';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            /*-------------------------------*/

            //Foot
            $html .= '<div class="foot col-12">';
            $html .= '
              <p align="right"><a title="' . __('Agence Pouco | Alsace', 'poucoimportusers') . '" target="_blanc" href="' . __('https://agence.pouco.ooo/', 'poucoimportusers') . '">' . __('©Pouco', 'poucoimportusers') . '</a></p>
            ';
            $html .= '</div>';

            $html .= '</div>'; //end row
            $html .= '</div>'; //end left

            $html .= '</div>'; //end row
            $html .= '</div>'; //end container-fluid

            $html .= '</div>'; //end poucoimportusers

            echo $html;
        }

        /**
          * Update list new users, update users and total users
          * @author Morgan JOURDIN
          * @return null
          */
        public function count_users()
        {
            $users = $this->read_csv();
            $this->totalUsers = count($users) - 1; //Intituler

            foreach ($users as $key => $value) {
                if ($key > 0) {
                    list($lastname, $firstname, $user_email, $user_url, $user_pass, $user_login, $user_nicename, $description, $role) = explode(';', $value);

                    if (!email_exists($user_email) && !username_exists($user_login)) {
                        $this->arrayNewUsers[$this->totalNewUsers] = rtrim($value);
                        $this->totalNewUsers++; //count nb new users
                    } else {
                        $this->arrayUpdatedUsers[$this->totalUpdateUsers] = rtrim($value);
                        $this->totalUpdateUsers++; //count nb updated user
                    }
                }
            }
        }

        /**
          * Ajax fonction to update user or update user
          * @author Morgan JOURDIN
          * @return null
          */
        public function users_update()
        {
            extract($_POST);

            list($last_name, $first_name, $user_email, $user_url, $user_pass, $user_login, $user_nicename, $description, $role) = explode(';', $listusers);

            if (!empty($user_login) && !empty($user_email)) {
                if (email_exists($user_email) !== false && username_exists($user_login) !== false) {
                    $id = email_exists($user_email);
                }

                if (!empty($description)) {
                    $description = sanitize_textarea_field($description);
                }

                $userdata = array(
                  'ID' => $id,
                  'user_login'  =>  (!empty($user_login)) ? sanitize_text_field($user_login) : '',
                  'user_nicename' => (!empty($user_nicename)) ? sanitize_text_field($user_nicename) : '',
                  'user_url' => (!empty($user_url)) ? sanitize_text_field($user_url) : '',
                  'user_email' => (!empty($user_email)) ? sanitize_email($user_email) : '',
                  'first_name' => (!empty($first_name)) ? sanitize_text_field($first_name) : '',
                  'last_name' => (!empty($last_name)) ? sanitize_text_field($last_name) : '',
                  'description' => $description,
                  'role' => (!empty($role)) ? sanitize_text_field($role) : ''
                );

                $user_id = wp_insert_user($userdata);

                //On success
                if (! is_wp_error($user_id)) {
                    wp_send_json_success(array(2002 => '#successupdate'));
                    die();
                } else {
                    wp_send_json_error(array(4037 => '#errorupdate', 'user' => $user_login));
                    die();
                }
            } else {
                wp_send_json_error(array(4042 => '#errorupdate', 'user' => $user_login));
            }

            die();
        }

        /**
          * Ajax fonction to create user or update user
          * @author Morgan JOURDIN
          * @return null
          */
        public function users_create()
        {
            extract($_POST);

            list($last_name, $first_name, $user_email, $user_url, $user_pass, $user_login, $user_nicename, $description, $role) = explode(';', $listusers);
            if (!empty($user_login) && !empty($user_email)) {
                if (!empty($description)) {
                    $description = sanitize_textarea_field($description);
                }

                $userdata = array(
                  'user_login'  =>  (!empty($user_login)) ? sanitize_text_field($user_login) : '',
                  'user_nicename' => (!empty($user_nicename)) ? sanitize_text_field($user_nicename) : '',
                  'user_url' => (!empty($user_url)) ? sanitize_text_field($user_url) : '',
                  'user_email' => (!empty($user_email)) ? sanitize_email($user_email) : '',
                  'first_name' => (!empty($first_name)) ? sanitize_text_field($first_name) : '',
                  'last_name' => (!empty($last_name)) ? sanitize_text_field($last_name) : '',
                  'description' => $description,
                  'role' => (!empty($role)) ? sanitize_text_field($role) : '',
                  'user_pass'   =>  (!empty($user_pass)) ? sanitize_text_field($user_pass) : wp_generate_password(12, true, true)  // When creating a new user, `user_pass` is expected.
                );

                $user_id = wp_insert_user($userdata);

                //On success
                if (! is_wp_error($user_id)) {
                    wp_new_user_notification($user_id);
                    wp_send_json_success(array(2001 => '#successcreate'));
                    die();
                } else {
                    wp_send_json_error(array(4037 => '#errorcreate', 'user' => $user_login));
                    die();
                }
            } else {
                wp_send_json_error(array(4039 => '#errorcreate', 'user' => $user_login));
            }

            die();
        }

        /**
          * Read CSV
          * @author Morgan JOURDIN
          * @return array $scv
          */
        public function read_csv()
        {
            $directory = POUCO_UPLOADS . self::$textdomain . '/';
            $target = glob($directory . "*.csv");
            $target = $target[0];

            $file = new SplFileObject($target, 'r');
            $file->seek(PHP_INT_MAX);

            $csv = file($target);

            return $csv;
        }

        /**
         * Ajax fonction for upload the CSV file
         * @author Morgan JOURDIN
         * @return JSON wp_send_json_error/wp_send_json_success
         */
        public function fileUpload()
        {
            $info = pathinfo($_FILES['file']['name']);
            $ext = $info['extension']; // get the extension of the file

            if ($ext !== 'csv') {
                wp_send_json_error(array(4033 => '#errorDownload')); //send error message with a code error
                die();
            }

            $target = POUCO_UPLOADS . self::$textdomain . '/';

            if (!is_file($target)) {
                mkdir($target, 0755);
            }

            //Remove all files before import the new csv
            $mask = $target . '*.csv';
            array_map("unlink", glob($mask)); //delete all file

            if (!$this->save_file_server($_FILES['file']['name'], $target, $_FILES['file']['tmp_name'])) {
                wp_send_json_error(array(4034 => '#errorDownload')); //send error message with a code error
                die();
            } else {
                $this->count_users();

                wp_send_json_success(
                    array(
                    2000 => '#successDownload',
                    'import' => array(
                      'listnewusers' => $this->arrayNewUsers,
                      'listupdateusers' => $this->arrayUpdatedUsers,
                      'totalnewusers' => $this->totalNewUsers,
                      'totalupdateusers' => $this->totalUpdateUsers,
                    )
                  )
                ); //send success message with an array
                die();
            }
            die();
        }

        /**
         * Usage: save_file_server($_FILE['file']['name'],'temp/',$_FILE['file']['tmp_name'])
         * @author Morgan JOURDIN
         * @param  string $origin
         * @param  string $dest
         * @param  string $tmp_name
         * @return bool
         */
        public function save_file_server($origin, $dest, $tmp_name)
        {
            $origin = strtolower(basename($origin));
            $fulldest = $dest.$origin;
            $filename = $origin;
            for ($i=1; file_exists($fulldest); $i++) {
                $fileext = (strpos($origin, '.')===false?'':'.'.substr(strrchr($origin, "."), 1));
                $filename = substr($origin, 0, strlen($origin)-strlen($fileext)).'['.$i.']'.$fileext;
                $fulldest = $dest.$newfilename;
            }

            if (move_uploaded_file($tmp_name, $fulldest)) {
                return $filename;
            }
            return false;
        }
    }
}
