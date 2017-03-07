<?php
namespace BiStorm;
namespace BiStorm\SLUG;

error_reporting(E_ERROR | E_WARNING | E_PARSE);

class Slug {
    public function __construct($namespace = "bistorm"){
        ## Properties
        $this->json_root = $namespace;
        $this->sh_root = "";
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
        $this->action_args = array();
        
        $slug_body = new \stdClass();
        
        ## STEP 1: Import slug.json
            $conf = file_get_contents('/var/www/slug/slug.json');
            $obj_conf = json_decode($conf);
            try{
               $this->{$namespace} = $obj_conf->{$namespace};
            } catch (Exception $ex) {
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\":\"Fore 0 fore! Swing but no hit!\"}}}";
                return $ex;
            }
        
        ## STEP 2: PARSE URL PATH AS ARRAY 
            $this->path_sectors = explode("/", $this->path);
            array_shift($this->path_sectors); // nginx proxy doesn't push '/slug/'
            $sector_size = sizeof($this->path_sectors);
            $last_sector = explode("?", $this->path_sectors[$sector_size-1]);
            $this->path_sectors[$sector_size-1] = $last_sector[0];

        
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
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\": '" . $this->path_sectors[1] . "/ did not match any slug namespaces.'}}}";
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
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\": '" . $this->path_sectors[2] . "/ did not match any slug apps.'}}}";
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
            if( ! $this->verifyAppActionPath( $this->slug_app_action_directives ) ) {
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\": '" . $this->path_sectors[2] . ": Your request did not match any slug app actions.'}}}";
                return false;    
            }

            
            ## Set the current action as object
            $this->setSlugAppAction( $this->slug_app_action_directives );

            
        ## STEP 4: 
        
        ## STEP 5: Unset json that shouldn't be public
        #unset( $this->{$namespace} );
        #unset ( $this->slug_apps );
        
    }
    
    public function exec() {
        if(empty((array)$this->action)) {
            $this->slug_response = "{\"slug\":{\"msg\":{\"error\": 'No action has been set for this directive.'}}}";  
        }
        ini_set('max_execution_time', 60*1);
        
        $args = $this->getActionArgs();
        $args_str = implode(" ", $args);

        $script = $this->slug_app_exec_dir . "/" . $this->action->name;
        
        $output = shell_exec( "bash " . $script . " " . "{$args_str}" );
        
        if ( $output == "" || $output === NULL) {
            if ($this->action->msg->failure== "@sandy") {
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\": \"" . trim(urlencode($output)) . "\"}}}";
            } else {
                $this->slug_response = "{\"slug\":{\"msg\":{\"error\": \"" . trim($this->action->msg->failure) . "\"}}}";
            }
        }
        
        if ($this->action->msg->success == "@sandy") {
            $this->slug_response = "{\"slug\":{\"msg\":{\"SLUG\": \"" . trim(urlencode($output)) . "\"}}}";
        } else {
            $this->slug_response = "{\"slug\":{\"msg\":{\"SLUG\": \"" . trim($this->action->msg->success) . "\"}}}";
        }
        $this->output();
    }
    
    public function output( $output_str = "") {
        header('Content-Type: application/json');
        if ( $output == "") {
            echo (string)$this->slug_response;
        } else {
            echo (string)$output_str;
        }
        
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
            $this->slug_response = "{\"slug\":{\"msg\":{\"error\":'Fore 0 fore! Swing but no hit!'}}}";
            return false;
        }
    }
    
    private function getActionArgs() {
        $args = array();
        foreach( $_GET as $arg => $val ) {
            if( strpos( $arg, "arg" ) !== FALSE ) {
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
    
    public function setSlugAppExecutionDir ( $slug_apps, $slug_app_name ) {
        $this->slug_app_exec_dir = $slug_apps->{$slug_app_name}->sh_root;
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
}

$trail = new Slug('bistorm');
$trail->exec();

exit;



/*
foreach ( $obj_conf->bistorm->slug as $thing => $value ) {
    
    $route = "/slug/" . $thing;
    $default_resp = json_encode($obj_conf->bistorm->slug);
    
    # 
    foreach ( $obj_conf->bistorm->slug->{$thing} as $app => $app_props ) {

        $route = $thing . '/' . $app;
        if( strpos($uri, $route) !== FALSE ) {
            $service_props['name'] = $app;
            $service_props['root'] = $obj_conf->bistorm->slug->{$thing}->{$app}->root;
            foreach ( $app_props->action as $action => $val ) {
                $service_props['actions'][$action] = $val;
            }
            
            if( empty($service_props) ) {
                echo json_encode($obj_conf->bistorm->slug);
                exit;
            } else {
                foreach ( $service_props['actions'] as $action => $v ) {
                    $route = '/' . $thing . '/' . $app . '/action/' . $v->route;
                    $include = '/' . $thing . '/' . $service_props['root'] . '/action/' . $v->route . '.php';
                    if( $uri == $route ) { 
                        include_once "/vagrant/slug" . $include;
                    }
                }
            }
            break;
        }
    }

    if( ! empty($service_props) ) {
        $action_url_args = explode('/action/', $uri);
        action($action_url_args);
    } else {
        echo $default_resp;
    }

}
*/

exit;


echo $conf;
exit;


$conf = file_get_contents('/var/www/slug/slug.json');
echo $conf;
exit;
include '/var/www/slug/include/common.php';
ini_set('max_execution_time', 5);
$old_path = getcwd();
chdir('/vagrant/bistorm/iot/hdhomerun');
$output = shell_exec("bash ./channel " . getChannel());
if ($output !== "") {
    $output = "<bistorm><slug><iot><hdhomerun><channel><status>1</status><output>" . $output . "</output></channel></hdhomerun></iot></slug></bistorm>";
}else {
    $output = "<bistorm><slug><iot><hdhomerun><channel><status>0</status><output>Sandy: I asked slug to change the channel, but we appear to have a sluck slug.</output></channel></hdhomerun></iot></slug></bistorm>";
}
echo $output;

?>