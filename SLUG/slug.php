<?php
namespace BiStorm;
namespace BiStorm\SLUG;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

/*
 * Note from SecOps: Do not modify the .htaccess permissions to slug.json 
 * You might forget you've done so and then expose the server environment's 
 * shell scripts to be discovered and potentiatially targeted maliciously.
 * Instead, you might create an instance of Slug and run var_dump($YOUR_INSTANCE).
 */

/*
 * CLASS SLUG 
 *  is not static-based. Please instantiate and access properties directly.
 *  ARGUMENTS 
 *      $namespace is the root of slug.json.  
 *      $app_init is an array that must contain the values below.
 *      Think about each value as part of a URL, but is accessible via PHP.
 *      A few included apps are given as examples.
 *      app_init[
 *          'app_namespace': '[iot,vcumux]', // Defined under the root namespace
 *          'app_name': '[hdhr, stream]', // Defined under the app_namespace
 *          'action': '[channel, check_flag]', // The action to take for the app
 *          'args': array('value1', 'value2') // Whatever argument values to pass to shell 
 *      ]
 * 
 *      NOTE: - the shell script must accept regular string values, usually contained in "$1" and "$2"
 * 
 */
class Slug {
    public function __construct($namespace = "bistorm", $app_init = array(), $response_yn = false){
        ## Properties
        $this->response_yn = $response_yn;
        $this->json_root = $namespace;
        $this->sh_root = "";
        $this->slug_response = "";
        $this->slug_root = "";
        $this->slug_response = "";
        $this->slug_namespaces = array();
        $this->slug_namespace = "";
        $this->slug_apps = new \stdClass();
        $this->slug_app_directives = array();
        $this->slug_app_name = "";
        $this->slug_app_web_root = "";
        $this->slug_app_action_directives = array();
        $this->slug_app_exec_dir = "";
        $this->path_sectors = array();
        $this->path = $_SERVER['REQUEST_URI'];
        $this->actions = new \stdClass();
        $this->action = new \stdClass();
        $this->error = 0;
        
        $slug_body = new \stdClass();
        
        ## STEP 1: Import slug.json
        $conf = file_get_contents('/var/www/slug/slug.json');
        $obj_conf = json_decode($conf);
        try{
           $this->{$namespace} = $obj_conf->{$namespace};
        } catch (Exception $ex) {
            $this->slug_response = "{\"slug\":{\"msg\":{\"error\":\"Fore 0 fore! Swing but no hit!\"}}}";
            $this->error = 1;
            $this->exec('', '404');
            return $ex;
        }
        
        ## STEP 2: PARSE URL PATH AS ARRAY or VALIDATE $app_init as OUR URL
        $this->path_sectors = explode("/", $this->path);
        array_shift($this->path_sectors); 
        $sector_size = sizeof($this->path_sectors);
        $last_sector = explode("?", $this->path_sectors[$sector_size-1]);
        $this->path_sectors[$sector_size-1] = $last_sector[0];
        
        # Validate and use $app_init (if it has been passed) instead of url
        if ( ! empty($app_init) ) {
            $this->path_sectors = array();
            try{
                $this->path_sectors = array('', $app_init['app_namespace'], $app_init['app_name'], 'action', $app_init['action']);
            } catch (Exception $ex) {
                throw new Exception("SLUG __METHOD__: app_init was passed in, but Slug could not contstruct an executable path.");
            }
            if( ! empty($app_init['args']) ) {
                foreach ( $app_init['args'] as $arg ) {
                    array_push($_GET, $arg);
                }
            }
            $this->path = implode('/', $this->path_sectors);
        }
        # Trim off .json at the end of url if it was requested
        if( strpos($this->path_sectors[4], ".json") !== FALSE ) {
            $json_ext = explode(".json", $this->path_sectors[4]);
            $this->path_sectors[4] = $json_ext[0];
        }
        
        ## STEP 3: Use getters and setters to parse JSON-PHP object
        ## Set shell executable root directory
        $this->sh_root = $this->{$namespace}->sh_root;

        ## Set temporary object body for traversing slug apps
        $slug_body = $this->getSlugBody( $namespace );

        ## Set slug local directory root
        $this->slug_root = $slug_body->web_root;

        ## Set the slug-specific namespaces for apps to prop slug_namespaces (i.e. [iot])
        $this->setSlugAppNamespaces( $slug_body );

        ## Verify if an namespace exists in the url
        if( ! $this->verifyAppNamespace( $this->slug_namespaces ) ) {
            $this->slug_response = (object)array('slug' => array('msg' => array('error' => "\"" . $this->path_sectors[1] . "/ did not match any slug namespaces.\"")));
            $this->error = 1;
            $this->exec('', '404');
            return false;
        }

        ## Set the validated slug namespace
        $this->setCurrentAppNamespace( $this->slug_namespaces );

        ## Set the webroot of the namespaced discovered App
        $this->setSlugNamespaceWebRoot( $slug_body, $this->slug_namespaces );

        ## Set the slug_apps object to the app listings under the slug namespace
        $this->setSlugAppsBody( $slug_body, $this->slug_namespace );

        ## Set the app directives 
        $this->setSlugAppDirectives( $this->slug_apps );

        ## Verify if an app name exists in the url
        if( ! $this->verifyAppPath( $this->slug_app_directives ) ) {
            $this->slug_response = (object)array('slug' => array('msg' => array('error' => "\"" . $this->path_sectors[2] . "/ did not match any slug apps.\"")));
            $this->error = 1;
            $this->exec('', '404');
            return false;
        }

        ## Set the verified app name
        $this->setSlugAppName( $this->slug_app_directives );

        ## Set the current app's web root
        $this->setSlugAppWebRoot( $this->slug_apps, $this->slug_app_name ); 

        ## Set the app execution directory
        $this->setSlugAppExecutionDir( $this->slug_apps, $this->slug_app_name );

        ## Set the app action directives
        $this->setSlugAppActionDirectives( $this->slug_apps, $this->slug_app_name );

        ## Set the current app action
        $this->setSlugAppActions( $this->slug_apps, $this->slug_app_name );

        ## Verify if an app action exists in the url
        if( ! $this->verifyAppActionPath( $this->slug_app_action_directives ) && $this->slug_response == "" ) {
            $this->error = 1;
            $this->slug_response = (object)array('slug' => array('msg' => array('error' => "\"" . $this->path_sectors[2] . ": Your request did not match any slug app actions.\"")));
            $this->exec('', '404');
            return false;    
        }

        ## Set the current action as object
        $this->setSlugAppAction( $this->slug_app_action_directives );
        
        if(empty((array)$this->action) && $this->slug_response == "") {
            $this->error = 1;
            $this->slug_response = (object)array('slug' => array('msg' => array('error' => "No action has been set for this directive.")));
            $this->exec('', '404');
            return false; 
        }
        
        ## STEP 4: Unset json that shouldn't be public
        unset( $this->{$namespace} );
        unset ( $this->slug_apps );
        unset ( $this->actions );
        
    }
    
    public function exec($response = true, $status = '200') {

        // Output early errors, typically 404, from __construct method
        if( ($this->response_yn || $response) && $this->slug_response != "") {
            $this->output($status);
            die();
        }
        
        ini_set('max_execution_time', 60*1);
        
        $args = $this->getActionArgs();

        $args_str = implode(" ", $args);

        $script = $this->slug_app_exec_dir . "/" . $this->action->name;

        $exec_resp = shell_exec( 'bash ' . $script . ' ' .  $args_str );

        // Failure block
        if ( $this->action->log !== "false" ) {
            if ( $exec_resp == NULL ) {
                if ($this->action->msg->failure == "@sandy") {
                    $this->slug_response = (object)array('slug' => array('msg' => array('error' => "\"" . trim(urlencode($exec_resp)) . "\"")));
                } else {
                    $this->slug_response = (object)array('slug' => array('msg' => array('error' => "\"" . trim($this->action->msg->failure) . "\"")));
                }
                
                if ( $response ) {
                    $this->output('', '500');
                }
                return $this;
            }
        } else {
            if ( $exec_resp == NULL ) {
                if ($this->action->msg->failure == "@sandy") {
                    $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => "Sandy says there was an error, but it\'s a private affair.")));
                } else {
                    $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => "A call was made, and something happened, but it was not what you humans call \'success\'.")));
                }
                
                if ( $response ) {
                    $this->output('', '500');
                }
                return $this;
            }
        }
        
        // Success block
        if ( $this->action->log !== "false" ) {
            if ( $this->action->msg->success == "@sandy" ) {
                $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => trim(urlencode($exec_resp)))));
            } else {
                $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => trim(urlencode($this->action->msg->success)))));
            }
        } else {
            if ( $this->action->msg->success == "@sandy" ) {
                $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => 'Sandy says this was a good call, but private.')));
            } else {
                $this->slug_response = (object)array('slug' => array('msg' => array('SLUG' => 'Successful call, but the response is set to private.')));
            }
        }
        
        if ( $response ) {
            $this->output();
        }
        
        return $this;   
    }
    
    public function output( $output_str = "", $status_code = '200' ) {
        header_remove();
        header("Cache-Control: no-cache, must-revalidate");
	header("Expires: 0");
        header('Content-Type: application/json');
        header('Status: ' . $status_code);
        
        // if you are doing ajax with application-json headers
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true) ? : [];
        }
        
        if ( $output == "") {
            $output = json_encode($this->slug_response, JSON_PRETTY_PRINT);
            echo $output;
        } else {
            $output = json_encode($output_str, JSON_PRETTY_PRINT);
            echo $output;
        }
        
        die();
    }
    
    ##
    #
    # GETTERS
    #
    ##
    
    private function getSlugBody($namespace) {
        ## Import slug.json
        try{
           return $this->{$namespace}->slug;
        } catch (Exception $ex) {
            $this->slug_response = (object)array('slug' => array('msg' => array('error' => "Fore 0 fore! Swing but no hit!")));
            return false;
        }
    }
    
    private function getActionArgs( ) {
        $args = array();
        if( empty($this->args) ) {
            $this->args = $_GET;
        }
        foreach( $this->args as $arg => $val ) {
            if( strpos( $arg, "arg" ) !== FALSE ) {
                $arg = trim($arg);
                $arg = urlencode($arg); 
                array_push( $args, $val );
            }
        }
        return $args;
    }
    
    ##
    #
    # SETTERS
    #
    ##
    
    public function setSlugAppNamespaces( $slug_body ) {
        foreach ( $slug_body as $slug_namespace => $body) {
           if ( $slug_namespace == "web_root" ) {
               continue;
           }
           array_push($this->slug_namespaces, $slug_namespace);
        }
        return;
    }
    
    public function setSlugNamespaceWebRoot( $body, $namespaces ) {
        foreach ( $namespaces as $slug_namespace ) {
           if( $this->path_sectors[1] == $slug_namespace ) {
               $this->slug_app_web_root = $body->$slug_namespace->sh_root;
               return;
           } 
        }
        return false;
    }
    
    public function setCurrentAppNamespace( $namespaces ) {
        foreach ( $namespaces as $slug_namespace ) {
           if( $this->path_sectors[1] == $slug_namespace ) {
               $this->slug_namespace = $this->path_sectors[1];
               return;
           }
        }
        return false;
    }
    
    public function setSlugAppsBody( $slug_body, $namespace ) {
        $this->slug_apps = $slug_body->{$namespace};
        return;
    }
    
    public function setSlugAppDirectives( $slug_apps ) {
        $paths = array();
        foreach ( $slug_apps as $appName => $appValues ) {
            if ( $appName == 'sh_root' ) {
                continue;
            }
            $path = '/' . $this->slug_namespace . '/' . $appName;
            array_push($paths, $path);
        }
        if( empty($paths) ) {
            return false;
        }
        $this->slug_app_directives = $paths;
        return;
    }
    
    public function setSlugAppName( $app_directives ) {
        foreach ( $app_directives as $path ) {
           if( '/' . $this->path_sectors[1] . '/' . $this->path_sectors[2] == $path ) {
               $app_name = explode("/", $path);
               $this->slug_app_name = $app_name[2];
               return;
           }
        }
        return false;
    }
    
    public function setSlugAppWebRoot( $slug_apps, $app_name ) {
        $this->slug_app_web_root = $slug_apps->{$app_name}->sh_root;
        return;
    }
    
    public function setSlugAppActionDirectives ( $slug_apps, $slug_app_name ) {
        $directives = array();   
        foreach ( $slug_apps->{$slug_app_name}->action as $action => $act_body ) {
            $path = '/' . $this->slug_namespace . '/' . $slug_app_name . '/action/' . $act_body->name;
            array_push( $directives, $path );
        }
        if( empty($directives) ) {
            return false;
        }
        $this->slug_app_action_directives = $directives;
        return true;
    }
    
    public function setSlugAppActions ( $slug_apps, $slug_app_name ) {
        foreach ( $slug_apps->{$slug_app_name}->action as $action => $act_body ) {
           $this->actions->{$action} = $act_body;
        } 
    }
    
    public function setSlugAppAction( $action_directives ) {
        foreach ( $action_directives as $directive ) {        
           if( strpos( $this->path, $directive ) !== FALSE ) {
               $dirs = explode( "/", $directive);
               $dir_count = sizeof($dirs);
               $actionPath = $dirs[$dir_count-1];           
               foreach ( $this->actions as $action ) {
                   if( $action->name == $actionPath ) {
                       $this->action = $action;
                       return;
                   }
               }
           }
        }
        return false;
    }
    
    public function setSlugAppExecutionDir ( $slug_apps, $app_name ) {
        $this->slug_app_exec_dir = $slug_apps->{$app_name}->sh_root;
    }
    
    ##
    #
    # VALIDATORS
    #
    ##
    
    public function verifyAppNamespace( $namespaces ) {
        try {
            foreach ( $namespaces as $slug_namespace ) {
                if( $this->path_sectors[1] == $slug_namespace ) {
                    return true;
                }
             }
        } catch (Exception $ex) {
            return $ex;
        }
        return false;
    }
    
    public function verifyAppPath( $app_directives ) {
        foreach ( $app_directives as $path ) {
           try {
               if( '/' . $this->path_sectors[1] . '/' . $this->path_sectors[2] == $path ) {
                    return true;
               }
           } catch (Exception $ex) {
               return $ex;
           }
        }
        return false;
    }
    
    public function verifyAppActionPath ( $app_actions ) {
        foreach ( $app_actions as $path ) {
           try {
              if( '/' . $this->path_sectors[1] . '/' . $this->path_sectors[2] . '/action/' . $this->path_sectors[4] == $path ) {
                return true;
              } 
           } catch (Exception $ex) {
              return $ex;
           }
        }
        return false;
    }
    
    /**
     * ACTIONS
     */    
    public function addActionArg($arg) {
        $arg = trim($arg);
        $arg = urlencode($arg); 
        array_push( $this->args, $val );
    }
}

?>