<?php
/**
 * Bootstrap.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

/**
 * bootstrap - ProcessMaker Bootstrap
 * this file is used initialize main variables, redirect and dispatch all requests
 */
// Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
define( 'PATH_SEP', '/' );

// Defining the Home Directory
$realdocuroot = str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] );
$docuroot = explode( PATH_SEP, $realdocuroot );

array_pop( $docuroot );
$pathhome = implode( PATH_SEP, $docuroot ) . PATH_SEP;

// try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
// in a normal installation you don't need to change it.
array_pop( $docuroot );
$pathTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP;

array_pop( $docuroot );
$pathOutTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP;

define( 'PATH_HOME', $pathhome );
define( 'PATH_TRUNK', $pathTrunk );
define( 'PATH_OUTTRUNK', $pathOutTrunk );

//we are focusing in have this behaivour
//1. if the uri is a existing file return the file inmediately
//2. if the uri point to png, jpg, js, or css mapped in other place, return it inmediately
//3. process the uri,

//here we are putting approved CONSTANTS, I mean constants be sure we need,
define( 'PATH_HTML', PATH_HOME . 'public_html' . PATH_SEP );

//this is the first path, if the file exists...
$request = substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI']));
$fileWithoutParam = explode("?",$request);
$requestFile = PATH_HTML . $fileWithoutParam[0];
if (file_exists($requestFile)) {
    if (!is_file($requestFile)) {
        header( "location: /errors/error404.php?url=" . urlencode( $_SERVER['REQUEST_URI'] ) );
        die;
    }
    $pos = strripos($request, ".") + 1;
    $size = strlen($request);
    if($pos < $size) {
        //if this file got an extension then assign the content
    	$ext_file = substr($request, $pos, $size);
        if ($ext_file == "gif" || $ext_file == "png" || $ext_file == "jpg") {
            $ext_file = 'image/'.$ext_file ;
        } elseif ($ext_file == "swf") {
            $ext_file = "application/x-shockwave-flash";
        } elseif ($ext_file == "json" || $ext_file == "htc" ) {
            $ext_file = "text/plain";
        } elseif ($ext_file == "htm" || $ext_file == "html" || $ext_file == "txt") {
            $ext_file = "text/html";
        } elseif ($ext_file == "doc" || $ext_file == "pdf" || $ext_file == "pm" || $ext_file == "po") {
            $ext_file = "application/octet-stream";
        } elseif ($ext_file == "tar") {
            $ext_file = "application/x-tar";
	    } elseif ($ext_file=="css") {
	        //may this line be innecesary, all the .css are been generated at run time
	        $ext_file = 'css/'.$ext_file;
	    } else {
	        $ext_file = "application/octet-stream";
	    }
	    header ('Content-Type: ' . $ext_file);
    }
    header ( 'Pragma: cache' );
    $mtime = filemtime ( $requestFile );
	$gmt_mtime = gmdate ( "D, d M Y H:i:s", $mtime ) . " GMT";
	header ( 'ETag: "' . md5 ( $mtime . $requestFile ) . '"' );
	header ( "Last-Modified: " . $gmt_mtime );
	header ( 'Cache-Control: public' );
    $userAgent = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
	if (preg_match ( "/msie/i", $userAgent )) {
		header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + 60 * 10 ) . " GMT" );
	} else {
	    header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + 90 * 60 * 60 * 24 ) . " GMT" );
	    if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] )) {
	        if ($_SERVER ['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
	            header ( 'HTTP/1.1 304 Not Modified' );
	        }
	    }
	    if (isset ( $_SERVER ['HTTP_IF_NONE_MATCH'] )) {
	        if (str_replace ( '"', '', stripslashes ( $_SERVER ['HTTP_IF_NONE_MATCH'] ) ) == md5 ( $mtime . $requestFile )) {
	            header ( "HTTP/1.1 304 Not Modified" );
            }
	    }
	}
    readfile($requestFile);
    die;
}

// Defining RBAC Paths constants
define( 'PATH_RBAC_HOME', PATH_TRUNK . 'rbac' . PATH_SEP );

// Defining Gulliver framework paths constants
define( 'PATH_GULLIVER_HOME', PATH_TRUNK . 'gulliver' . PATH_SEP );
define( 'PATH_GULLIVER', PATH_GULLIVER_HOME . 'system' . PATH_SEP ); //gulliver system classes
define( 'PATH_GULLIVER_BIN', PATH_GULLIVER_HOME . 'bin' . PATH_SEP ); //gulliver bin classes
define( 'PATH_TEMPLATE', PATH_GULLIVER_HOME . 'templates' . PATH_SEP );
define( 'PATH_THIRDPARTY', PATH_GULLIVER_HOME . 'thirdparty' . PATH_SEP );
define( 'PATH_RBAC', PATH_RBAC_HOME . 'engine' . PATH_SEP . 'classes' . PATH_SEP ); //to enable rbac version 2
define( 'PATH_RBAC_CORE', PATH_RBAC_HOME . 'engine' . PATH_SEP );

// Defining PMCore Path constants
define( 'PATH_CORE', PATH_HOME . 'engine' . PATH_SEP );
define( 'PATH_SKINS', PATH_CORE . 'skins' . PATH_SEP );
define( 'PATH_SKIN_ENGINE', PATH_CORE . 'skinEngine' . PATH_SEP );
define( 'PATH_METHODS', PATH_CORE . 'methods' . PATH_SEP );
define( 'PATH_XMLFORM', PATH_CORE . 'xmlform' . PATH_SEP );
define( 'PATH_CONFIG', PATH_CORE . 'config' . PATH_SEP );
define( 'PATH_PLUGINS', PATH_CORE . 'plugins' . PATH_SEP );
define( 'PATH_HTMLMAIL', PATH_CORE . 'html_templates' . PATH_SEP );
define( 'PATH_TPL', PATH_CORE . 'templates' . PATH_SEP );
define( 'PATH_TEST', PATH_CORE . 'test' . PATH_SEP );
define( 'PATH_FIXTURES', PATH_TEST . 'fixtures' . PATH_SEP );
define( 'PATH_RTFDOCS', PATH_CORE . 'rtf_templates' . PATH_SEP );
define( 'PATH_DYNACONT', PATH_CORE . 'content' . PATH_SEP . 'dynaform' . PATH_SEP );
//define( 'PATH_LANGUAGECONT',PATH_CORE . 'content' . PATH_SEP . 'languages' . PATH_SEP );
define( 'SYS_UPLOAD_PATH', PATH_HOME . "public_html/files/" );
define( 'PATH_UPLOAD', PATH_HTML . 'files' . PATH_SEP );

define( 'PATH_WORKFLOW_MYSQL_DATA', PATH_CORE . 'data' . PATH_SEP . 'mysql' . PATH_SEP );
define( 'PATH_RBAC_MYSQL_DATA', PATH_RBAC_CORE . 'data' . PATH_SEP . 'mysql' . PATH_SEP );
define( 'FILE_PATHS_INSTALLED', PATH_CORE . 'config' . PATH_SEP . 'paths_installed.php' );
define( 'PATH_WORKFLOW_MSSQL_DATA', PATH_CORE . 'data' . PATH_SEP . 'mssql' . PATH_SEP );
define( 'PATH_RBAC_MSSQL_DATA', PATH_RBAC_CORE . 'data' . PATH_SEP . 'mssql' . PATH_SEP );
define( 'PATH_CONTROLLERS', PATH_CORE . 'controllers' . PATH_SEP );
define( 'PATH_SERVICES_REST', PATH_CORE . 'services' . PATH_SEP . 'rest' . PATH_SEP );

//TEST
//if($_SERVER['REQUEST_URI'] == "/sysworkflow/en/classic/processes/mainInit")
//{
//    echo PATH_TRUNK . "engine/methods/processes/mainInit.php";
//}

// include Gulliver Class
require_once (PATH_GULLIVER . PATH_SEP . 'class.bootstrap.php');

if (file_exists( FILE_PATHS_INSTALLED )) {

    // include the server installed configuration
    require_once FILE_PATHS_INSTALLED;

    // defining system constant when a valid server environment exists
    define( 'PATH_LANGUAGECONT', PATH_DATA . "META-INF" . PATH_SEP );
    define( 'PATH_CUSTOM_SKINS', PATH_DATA . 'skins' . PATH_SEP );
    define( 'PATH_TEMPORAL', PATH_C . 'dynEditor/' );
    define( 'PATH_DB', PATH_DATA . 'sites' . PATH_SEP );

    // smarty constants
    define( 'PATH_SMARTY_C', PATH_C . 'smarty' . PATH_SEP . 'c' );
    define( 'PATH_SMARTY_CACHE', PATH_C . 'smarty' . PATH_SEP . 'cache' );
/* To do:
    if (! is_dir( PATH_SMARTY_C )) {
        G::mk_dir( PATH_SMARTY_C );
    }

    if (! is_dir( PATH_SMARTY_CACHE )) {
        G::mk_dir( PATH_SMARTY_CACHE );
    }
    */
}

// set include path
set_include_path( PATH_CORE . PATH_SEPARATOR .
                  PATH_THIRDPARTY . PATH_SEPARATOR .
                  PATH_THIRDPARTY . 'pear' . PATH_SEPARATOR .
                  PATH_RBAC_CORE . PATH_SEPARATOR .
                  get_include_path()
);

/**
 * Global definitions, before it was the defines.php file
 */

// URL Key
define( "URL_KEY", 'c0l0s40pt1mu59r1m3' );

// Other definitions
define( 'TIMEOUT_RESPONSE', 100 ); //web service timeout
define( 'APPLICATION_CODE', 'ProcessMaker' ); //to login like workflow system
define( 'MAIN_POFILE', 'processmaker' );
define( 'PO_SYSTEM_VERSION', 'PM 4.0.1' );

$G_CONTENT = NULL;
$G_MESSAGE = "";
$G_MESSAGE_TYPE = "info";
$G_MENU_SELECTED = - 1;
$G_MAIN_MENU = "default";

// Environment definitions
define( 'G_PRO_ENV', 'PRODUCTION' );
define( 'G_DEV_ENV', 'DEVELOPMENT' );
define( 'G_TEST_ENV', 'TEST' );

// Number of files per folder at PATH_UPLOAD (cases documents)
define( 'APPLICATION_DOCUMENTS_PER_FOLDER', 1000 );

// Server of ProcessMaker Library
define( 'PML_SERVER', 'http://library.processmaker.com' );
define( 'PML_WSDL_URL', PML_SERVER . '/syspmLibrary/en/green/services/wsdl' );
define( 'PML_UPLOAD_URL', PML_SERVER . '/syspmLibrary/en/green/services/uploadProcess' );
define( 'PML_DOWNLOAD_URL', PML_SERVER . '/syspmLibrary/en/green/services/download' );

// starting session
session_start();

$config = Bootstrap::getSystemConfiguration();

$e_all = defined( 'E_DEPRECATED' ) ? E_ALL & ~ E_DEPRECATED : E_ALL;
$e_all = defined( 'E_STRICT' ) ? $e_all & ~ E_STRICT : $e_all;
$e_all = $config['debug'] ? $e_all : $e_all & ~ E_NOTICE;

// Do not change any of these settings directly, use env.ini instead
ini_set( 'display_errors', $config['debug'] );
ini_set( 'error_reporting', $e_all );
ini_set( 'short_open_tag', 'On' );
ini_set( 'default_charset', "UTF-8" );
ini_set( 'memory_limit', $config['memory_limit'] );
ini_set( 'soap.wsdl_cache_enabled', $config['wsdl_cache'] );
ini_set( 'date.timezone', $config['time_zone'] );

define( 'DEBUG_SQL_LOG', $config['debug_sql'] );
define( 'DEBUG_TIME_LOG', $config['debug_time'] );
define( 'DEBUG_CALENDAR_LOG', $config['debug_calendar'] );
define( 'MEMCACHED_ENABLED', $config['memcached'] );
define( 'MEMCACHED_SERVER', $config['memcached_server'] );
define( 'TIME_ZONE', $config['time_zone'] );

// IIS Compatibility, SERVER_ADDR doesn't exist on that env, so we need to define it.
$_SERVER['SERVER_ADDR'] = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME'];

//to do: make different environments.  sys

//check if it is a installation instance
if (! defined( 'PATH_C' )) {
    // is a intallation instance, so we need to define PATH_C and PATH_LANGUAGECONT constants temporarily
    define( 'PATH_C', (rtrim( Bootstrap::sys_get_temp_dir(), PATH_SEP ) . PATH_SEP) );
    define( 'PATH_LANGUAGECONT', PATH_HOME . 'engine/content/languages/' );
}

// defining Virtual URLs
$virtualURITable = array ();
$virtualURITable['/plugin/(*)'] = 'plugin';
$virtualURITable['/(sys*)/(*.js)'] = 'jsMethod';
$virtualURITable['/js/(*)'] = PATH_GULLIVER_HOME . 'js/';
$virtualURITable['/jscore/(*)'] = PATH_CORE . 'js/';

if (defined( 'PATH_C' )) {
    $virtualURITable['/jsform/(*.js)'] = PATH_C . 'xmlform/';
    $virtualURITable['/extjs/(*)'] = PATH_C . 'ExtJs/';
}

$virtualURITable['/htmlarea/(*)'] = PATH_THIRDPARTY . 'htmlarea/';
//$virtualURITable['/sys[a-zA-Z][a-zA-Z0-9]{0,}()/'] = 'sysNamed';
$virtualURITable['/(sys*)'] = FALSE;
$virtualURITable['/errors/(*)'] = PATH_GULLIVER_HOME . 'methods/errors/';
$virtualURITable['/gulliver/(*)'] = PATH_GULLIVER_HOME . 'methods/';
$virtualURITable['/controls/(*)'] = PATH_GULLIVER_HOME . 'methods/controls/';
$virtualURITable['/html2ps_pdf/(*)'] = PATH_THIRDPARTY . 'html2ps_pdf/';
//$virtualURITable['/images/'] = 'errorFile';
//$virtualURITable['/skins/'] = 'errorFile';
//$virtualURITable['/files/'] = 'errorFile';
$virtualURITable['/rest/(*)'] = 'rest-service';
$virtualURITable['/update/(*)'] = PATH_GULLIVER_HOME . 'methods/update/';
//$virtualURITable['/(*)'] = PATH_HTML;
$virtualURITable['/css/(*)'] = PATH_HTML . 'css/'; //ugly
$virtualURITable['/[a-zA-Z][a-zA-Z0-9]{0,}/'] = 'errorFile';

$isRestRequest = false;
// Verify if we need to redirect or stream the file, if G:VirtualURI returns true means we are going to redirect the page
if (Bootstrap::virtualURI( $_SERVER['REQUEST_URI'], $virtualURITable, $realPath )) {
    // review if the file requested belongs to public_html plugin
    if (substr( $realPath, 0, 6 ) == 'plugin') {
        // Another way to get the path of Plugin public_html and stream the correspondent file, By JHL Jul 14, 08
        // TODO: $pathsQuery will be used?
        $pathsQuery = '';
        // Get the query side
        // Did we use this variable $pathsQuery for something??
        $forQuery = explode( "?", $realPath );
        if (isset( $forQuery[1] )) {
            $pathsQuery = $forQuery[1];
        }

        //Get that path in array
        $paths = explode( PATH_SEP, $forQuery[0] );
        //remove the "plugin" word from
        $paths[0] = substr( $paths[0], 6 );
        //Get the Plugin Folder, always the first element
        $pluginFolder = array_shift( $paths );
        //The other parts are the realpath into public_html (no matter how many elements)
        $filePath = implode( PATH_SEP, $paths );
        $pluginFilename = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'public_html' . PATH_SEP . $filePath;

        if (file_exists( $pluginFilename )) {
            Bootstrap::streamFile( $pluginFilename );
        }
        die();
    }

    $requestUriArray = explode( "/", $_SERVER['REQUEST_URI'] );

    if ((isset( $requestUriArray[1] )) && ($requestUriArray[1] == 'skin')) {
        // This will allow to public images of Custom Skins, By JHL Feb 28, 11
        $pathsQuery = "";
        // Get the query side
        // This way we remove garbage
        $forQuery = explode( "?", $realPath );
        if (isset( $forQuery[1] )) {
            $pathsQuery = $forQuery[1];
        }

        //Get that path in array
        $paths = explode( PATH_SEP, $forQuery[0] );
        $fileToBeStreamed = str_replace( "/skin/", PATH_CUSTOM_SKINS, $_SERVER['REQUEST_URI'] );

        if (file_exists( $fileToBeStreamed )) {
            Bootstrap::streamFile( $fileToBeStreamed );
        }
        die();
    }
    switch ($realPath) {
        case 'jsMethod':
            Bootstrap::parseURI( getenv( "REQUEST_URI" ) );
            $filename = PATH_METHODS . SYS_COLLECTION . '/' . SYS_TARGET . '.js';
            Bootstrap::streamFile( $filename );
            die();
            break;
        case 'errorFile':
            header( "location: /errors/error404.php?url=" . urlencode( $_SERVER['REQUEST_URI'] ) );
            if (DEBUG_TIME_LOG)
                Bootstrap::logTimeByPage(); //log this page
            die();
            break;
        default:
            if (substr( $realPath, 0, 12 ) == 'rest-service') {
                $isRestRequest = true;
            } else {
                $realPath = explode( '?', $realPath );
                $realPath[0] .= strpos( basename( $realPath[0] ), '.' ) === false ? '.php' : '';
                Bootstrap::streamFile( $realPath[0] );
                die();
            }
    }
} //virtual URI parser

// the request correspond to valid php page, now parse the URI
Bootstrap::parseURI( getenv( "REQUEST_URI" ), $isRestRequest );

if (Bootstrap::isPMUnderUpdating()) {
    header( "location: /update/updating.php" );
    if (DEBUG_TIME_LOG)
        Bootstrap::logTimeByPage();
    die();
}

// verify if index.html exists
if (! file_exists( PATH_HTML . 'index.html' )) { // if not, create it from template
    file_put_contents( PATH_HTML . 'index.html', Bootstrap::parseTemplate( PATH_TPL . 'index.html', array ('lang' => SYS_LANG,'skin' => SYS_SKIN
    ) ) );
}

define( 'SYS_URI', '/sys' . SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' );

// defining the serverConf singleton
if (defined( 'PATH_DATA' ) && file_exists( PATH_DATA )) {
    //Instance Server Configuration Singleton
    Bootstrap::LoadClass( 'serverConfiguration' );
    $oServerConf = & serverConf::getSingleton();
}

// Call Gulliver Classes
Bootstrap::LoadThirdParty( 'smarty/libs', 'Smarty.class' );

//testing the autoloader feature
spl_autoload_register(array(Bootstrap, 'autoloadClass'));
Bootstrap::registerClass('headPublisher', PATH_GULLIVER . "class.headPublisher.php");
Bootstrap::registerClass('G', PATH_GULLIVER . "class.g.php");
Bootstrap::registerClass('publisher', PATH_GULLIVER . "class.publisher.php");
Bootstrap::registerClass('xmlform', PATH_GULLIVER . "class.xmlform.php");
Bootstrap::registerClass('XmlForm_Field', PATH_GULLIVER . "class.xmlform.php");
Bootstrap::registerClass('xmlformExtension', PATH_GULLIVER . "class.xmlformExtension.php");
Bootstrap::registerClass('form',         PATH_GULLIVER . "class.form.php");
Bootstrap::registerClass('menu',         PATH_GULLIVER . "class.menu.php");
Bootstrap::registerClass('Xml_Document', PATH_GULLIVER . "class.xmlDocument.php");
Bootstrap::registerClass('DBSession',    PATH_GULLIVER . "class.dbsession.php");
Bootstrap::registerClass('DBConnection', PATH_GULLIVER . "class.dbconnection.php");
Bootstrap::registerClass('DBRecordset',  PATH_GULLIVER . "class.dbrecordset.php");
Bootstrap::registerClass('DBTable',      PATH_GULLIVER . "class.dbtable.php");
Bootstrap::registerClass('xmlMenu',      PATH_GULLIVER . "class.xmlMenu.php");
Bootstrap::registerClass('XmlForm_Field_XmlMenu', PATH_GULLIVER . "class.xmlMenu.php");
Bootstrap::registerClass('XmlForm_Field_HTML',  PATH_GULLIVER . "class.dvEditor.php");
Bootstrap::registerClass('Controller',          PATH_GULLIVER . "class.controller.php");
Bootstrap::registerClass('HttpProxyController', PATH_GULLIVER . "class.httpProxyController.php");
Bootstrap::registerClass('templatePower',            PATH_GULLIVER . "class.templatePower.php");
Bootstrap::registerClass('XmlForm_Field_SimpleText', PATH_GULLIVER . "class.xmlformExtension.php");
Bootstrap::registerClass('System',        PATH_HOME . "engine/classes/class.system.php");
Bootstrap::registerClass('Propel',          PATH_THIRDPARTY . "propel/Propel.php");
Bootstrap::registerClass('Creole',          PATH_THIRDPARTY . "creole/Creole.php");


//DATABASE propel classes used in 'Cases' Options
Bootstrap::registerClass('Entity_Base',         PATH_HOME . "engine/classes/entities/Base.php");

Bootstrap::registerClass('BaseContent',        PATH_HOME . "engine/classes/model/om/BaseContent.php");
Bootstrap::registerClass('Content',            PATH_HOME . "engine/classes/model/Content.php");
Bootstrap::registerClass('BaseApplication',    PATH_HOME . "engine/classes/model/om/BaseApplication.php");
Bootstrap::registerClass('ApplicationPeer',    PATH_HOME . "engine/classes/model/ApplicationPeer.php");
Bootstrap::registerClass('Application',        PATH_HOME . "engine/classes/model/Application.php");

Bootstrap::registerClass('BaseAppDelegation',  PATH_HOME . "engine/classes/model/om/BaseAppDelegation.php");
Bootstrap::registerClass('BaseHoliday',        PATH_HOME . "engine/classes/model/om/BaseHoliday.php");
Bootstrap::registerClass('BaseHolidayPeer',    PATH_HOME . "engine/classes/model/om/BaseHolidayPeer.php");
Bootstrap::registerClass('BaseTask',           PATH_HOME . "engine/classes/model/om/BaseTask.php");
Bootstrap::registerClass('BaseTaskPeer',       PATH_HOME . "engine/classes/model/om/BaseTaskPeer.php");
Bootstrap::registerClass('HolidayPeer',        PATH_HOME . "engine/classes/model/HolidayPeer.php");
Bootstrap::registerClass('Holiday',            PATH_HOME . "engine/classes/model/Holiday.php");
Bootstrap::registerClass('Task',               PATH_HOME . "engine/classes/model/Task.php");
Bootstrap::registerClass('TaskPeer',           PATH_HOME . "engine/classes/model/TaskPeer.php");
Bootstrap::registerClass('dates',              PATH_HOME . "engine/classes/class.dates.php");
Bootstrap::registerClass('AppDelegation',      PATH_HOME . "engine/classes/model/AppDelegation.php");
Bootstrap::registerClass('AppDelegationPeer',  PATH_HOME . "engine/classes/model/AppDelegationPeer.php");
Bootstrap::registerClass('BaseAppDelay',       PATH_HOME . "engine/classes/model/om/BaseAppDelay.php");
Bootstrap::registerClass('AppDelayPeer',       PATH_HOME . "engine/classes/model/AppDelayPeer.php");
Bootstrap::registerClass('AppDelay',           PATH_HOME . "engine/classes/model/AppDelay.php");
Bootstrap::registerClass('BaseAdditionalTables',PATH_HOME . "engine/classes/model/om/BaseAdditionalTables.php");
Bootstrap::registerClass('AdditionalTables',   PATH_HOME . "engine/classes/model/AdditionalTables.php");
Bootstrap::registerClass('BaseAppCacheView',   PATH_HOME . "engine/classes/model/om/BaseAppCacheView.php");
Bootstrap::registerClass('AppCacheView',       PATH_HOME . "engine/classes/model/AppCacheView.php");
Bootstrap::registerClass('AppCacheViewPeer',   PATH_HOME . "engine/classes/model/AppCacheViewPeer.php");

Bootstrap::registerClass('BaseInputDocument',  PATH_HOME . "engine/classes/model/om/BaseInputDocument.php");
Bootstrap::registerClass('InputDocument',      PATH_HOME . "engine/classes/model/InputDocument.php");
Bootstrap::registerClass('BaseAppDocument',    PATH_HOME . "engine/classes/model/om/BaseAppDocument.php");
Bootstrap::registerClass('AppDocument',        PATH_HOME . "engine/classes/model/AppDocument.php");
Bootstrap::registerClass('AppDocumentPeer',    PATH_HOME . "engine/classes/model/AppDocumentPeer.php");

Bootstrap::registerClass('BaseAppEvent',       PATH_HOME . "engine/classes/model/om/BaseAppEvent.php");
Bootstrap::registerClass('AppEvent',           PATH_HOME . "engine/classes/model/AppEvent.php");

Bootstrap::registerClass('BaseAppHistory',     PATH_HOME . "engine/classes/model/om/BaseAppHistory.php");
Bootstrap::registerClass('AppHistory',         PATH_HOME . "engine/classes/model/AppHistory.php");

Bootstrap::registerClass('BaseAppMessage',     PATH_HOME . "engine/classes/model/om/BaseAppMessage.php");
Bootstrap::registerClass('AppMessage',         PATH_HOME . "engine/classes/model/AppMessage.php");

Bootstrap::registerClass('BaseAppNotes',        PATH_HOME . "engine/classes/model/om/BaseAppNotes.php");
Bootstrap::registerClass('AppNotes',            PATH_HOME . "engine/engine/classes/model/AppNotes.php");

Bootstrap::registerClass('BaseAppOwner',        PATH_HOME . "engine/classes/model/om/BaseAppOwner.php");
Bootstrap::registerClass('AppOwner',            PATH_HOME . "engine/classes/model/AppOwner.php");

Bootstrap::registerClass('BaseAppSolrQueue',    PATH_HOME . "engine/classes/model/om/BaseAppSolrQueue.php");
Bootstrap::registerClass('Entity_AppSolrQueue', PATH_HOME . "engine/classes/entities/AppSolrQueue.php");
Bootstrap::registerClass('AppSolrQueue',        PATH_HOME . "engine/classes/model/AppSolrQueue.php");

Bootstrap::registerClass('BaseAppThread',       PATH_HOME . "engine/classes/model/om/BaseAppThread.php");
Bootstrap::registerClass('AppThread',           PATH_HOME . "engine/classes/model/AppThread.php");
Bootstrap::registerClass('AppThreadPeer',       PATH_HOME . "engine/classes/model/AppThreadPeer.php");

Bootstrap::registerClass('BaseCaseTracker',     PATH_HOME . "engine/classes/model/om/BaseCaseTracker.php");
Bootstrap::registerClass('CaseTracker',         PATH_HOME . "engine/classes/model/CaseTracker.php");

Bootstrap::registerClass('BaseCaseTrackerObject',PATH_HOME . "engine/classes/model/om/BaseCaseTrackerObject.php");
Bootstrap::registerClass('CaseTrackerObject',    PATH_HOME . "engine/classes/model/CaseTrackerObject.php");

Bootstrap::registerClass('BaseConfiguration',   PATH_HOME . "engine/classes/model/om/BaseConfiguration.php");
Bootstrap::registerClass('Configuration',       PATH_HOME . "engine/classes/model/Configuration.php");

Bootstrap::registerClass('BaseDbSource',        PATH_HOME . "engine/classes/model/om/BaseDbSource.php");
Bootstrap::registerClass('DbSource',            PATH_HOME . "engine/classes/model/DbSource.php");

Bootstrap::registerClass('XMLDB',              PATH_HOME . "engine/classes/class.xmlDb.php");
Bootstrap::registerClass('dynaFormHandler',    PATH_GULLIVER . "class.dynaformhandler.php");
Bootstrap::registerClass('DynaFormField',      PATH_HOME . "engine/classes/class.dynaFormField.php");
Bootstrap::registerClass('BaseDynaform',       PATH_HOME . "engine/classes/model/om/BaseDynaform.php");
Bootstrap::registerClass('Dynaform',           PATH_HOME . "engine/classes/model/Dynaform.php");
Bootstrap::registerClass('DynaformPeer',       PATH_HOME . "engine/classes/model/DynaformPeer.php");

Bootstrap::registerClass('BaseFields',         PATH_HOME . "engine/classes/model/om/BaseFields.php");
Bootstrap::registerClass('Fields',             PATH_HOME . "engine/classes/model/Fields.php");

Bootstrap::registerClass('BaseGroupUser',      PATH_HOME . "engine/classes/model/om/BaseGroupUser.php");
Bootstrap::registerClass('Groupwf',            PATH_HOME . "engine/classes/model/Groupwf.php");
Bootstrap::registerClass('GroupUser',          PATH_HOME . "engine/classes/model/GroupUser.php");

Bootstrap::registerClass('BaseInputDocumentPeer',PATH_HOME . 'engine/classes/model/om/BaseInputDocumentPeer.php');
Bootstrap::registerClass('InputDocumentPeer',  PATH_HOME . 'classes/model/InputDocumentPeer.php');

Bootstrap::registerClass('BaseIsoCountry',     PATH_HOME . "engine/classes/model/om/BaseIsoCountry.php");
Bootstrap::registerClass('IsoCountry',         PATH_HOME . "engine/classes/model/IsoCountry.php");
Bootstrap::registerClass('BaseTranslation',    PATH_HOME . "engine/classes/model/om/BaseTranslation.php");
Bootstrap::registerClass('Translation',        PATH_HOME . "engine/classes/model/Translation.php");
Bootstrap::registerClass('BaseLanguage',       PATH_HOME . "engine/classes/model/om/BaseLanguage.php");
Bootstrap::registerClass('Language',           PATH_HOME . "engine/classes/model/Language.php");

Bootstrap::registerClass('BaseObjectPermission',PATH_HOME . "engine/classes/model/om/BaseObjectPermission.php");
Bootstrap::registerClass('ObjectPermission',    PATH_HOME . "engine/classes/model/ObjectPermission.php");
Bootstrap::registerClass('ObjectPermissionPeer',PATH_HOME . "engine/classes/model/ObjectPermissionPeer.php");

Bootstrap::registerClass('BaseOutputDocument',  PATH_HOME . "engine/classes/model/om/BaseOutputDocument.php");
Bootstrap::registerClass('OutputDocument',      PATH_HOME . "engine/classes/model/OutputDocument.php");

Bootstrap::registerClass('BaseProcess',         PATH_HOME . "engine/classes/model/om/BaseProcess.php");
Bootstrap::registerClass('BaseProcessCategory', PATH_HOME . "engine/classes/model/om/BaseProcessCategory.php");
Bootstrap::registerClass('ProcessCategory',     PATH_HOME . "engine/classes/model/ProcessCategory.php");
Bootstrap::registerClass('ProcessCategoryPeer', PATH_HOME . "engine/classes/model/ProcessCategoryPeer.php");
Bootstrap::registerClass('ProcessPeer',         PATH_HOME . "engine/classes/model/ProcessPeer.php");
Bootstrap::registerClass('Process',             PATH_HOME . "engine/classes/model/Process.php");

Bootstrap::registerClass('BaseProcessUser',     PATH_HOME . "engine/classes/model/om/BaseProcessUser.php");
Bootstrap::registerClass('ProcessUser',         PATH_HOME . "engine/classes/model/ProcessUser.php");

Bootstrap::registerClass('BaseReportTable',     PATH_HOME . "engine/classes/model/om/BaseReportTable.php");
Bootstrap::registerClass('ReportTable',         PATH_HOME . "engine/classes/model/ReportTable.php");
Bootstrap::registerClass('ReportTablePeer',     PATH_HOME . "engine/classes/model/ReportTablePeer.php");

Bootstrap::registerClass('BaseReportVar',       PATH_HOME . "engine/classes/model/om/BaseReportVar.php");
Bootstrap::registerClass('ReportVar',           PATH_HOME . "engine/classes/model/ReportVar.php");

Bootstrap::registerClass('BaseRoute',           PATH_HOME . "engine/classes/model/om/BaseRoute.php");
Bootstrap::registerClass('Route',               PATH_HOME . "engine/classes/model/Route.php");

Bootstrap::registerClass('BaseStep',            PATH_HOME . "engine/classes/model/om/BaseStep.php");
Bootstrap::registerClass('Step',                PATH_HOME . "engine/classes/model/Step.php");
Bootstrap::registerClass('StepPeer',            PATH_HOME . "engine/classes/model/StepPeer.php");

Bootstrap::registerClass('BaseStepSupervisor',  PATH_HOME . "engine/classes/model/om/BaseStepSupervisor.php");
Bootstrap::registerClass('StepSupervisor',      PATH_HOME . "engine/classes/model/StepSupervisor.php");

Bootstrap::registerClass('BaseStepTrigger',     PATH_HOME . "engine/classes/model/om/BaseStepTrigger.php");
Bootstrap::registerClass('StepTrigger',         PATH_HOME . "engine/classes/model/StepTrigger.php");
Bootstrap::registerClass('StepTriggerPeer',     PATH_HOME . "engine/classes/model/StepTriggerPeer.php");

Bootstrap::registerClass('SolrRequestData',     PATH_HOME . "engine/classes/entities/SolrRequestData.php");

Bootstrap::registerClass('SolrUpdateDocument',  PATH_HOME . "engine/classes/entities/SolrUpdateDocument.php");

Bootstrap::registerClass('BaseSubApplication',  PATH_HOME . "engine/classes/model/om/BaseSubApplication.php");
Bootstrap::registerClass('SubApplication',      PATH_HOME . "engine/classes/model/SubApplication.php");

Bootstrap::registerClass('BaseTask',            PATH_HOME . "engine/classes/model/om/BaseTask.php");
Bootstrap::registerClass('Task',                PATH_HOME . "engine/classes/model/Task.php");

Bootstrap::registerClass('BaseTaskUser',        PATH_HOME . "engine/classes/model/om/BaseTaskUser.php");
Bootstrap::registerClass('TaskUserPeer',        PATH_HOME . "engine/classes/model/TaskUserPeer.php");
Bootstrap::registerClass('TaskUser',            PATH_HOME . "engine/classes/model/TaskUser.php");

Bootstrap::registerClass('BaseTriggers',        PATH_HOME . "engine/classes/model/om/BaseTriggers.php");
Bootstrap::registerClass('Triggers',            PATH_HOME . "engine/classes/model/Triggers.php");
Bootstrap::registerClass('TriggersPeer',        PATH_HOME . "engine/classes/model/TriggersPeer.php");

Bootstrap::registerClass('BaseUsers',           PATH_HOME . "engine/classes/model/om/BaseUsers.php");
Bootstrap::registerClass('IsoCountry',          PATH_HOME . "engine/classes/model/IsoCountry.php");
Bootstrap::registerClass('BaseIsoSubdivision',  PATH_HOME . "engine/classes/model/om/BaseIsoSubdivision.php");
Bootstrap::registerClass('IsoSubdivision',      PATH_HOME . "engine/classes/model/IsoSubdivision.php");
Bootstrap::registerClass('BaseIsoLocation',     PATH_HOME . "engine/classes/model/om/BaseIsoLocation.php");
Bootstrap::registerClass('Users',               PATH_HOME . "engine/classes/model/Users.php");
Bootstrap::registerClass('UsersPeer',           PATH_HOME . "engine/classes/model/UsersPeer.php");

Bootstrap::registerClass('Xml_Node',            PATH_GULLIVER . "class.xmlDocument.php");

//Bootstrap::registerClass('Cases',               PATH_HOME . "engine/classes/class.case.php");
//Bootstrap::registerClass('configuration',       PATH_HOME . "engine/classes/class.configuration.php");

//Bootstrap::registerClass('LoginLog',          PATH_HOME . "engine/classes/model/LoginLog.php");
//Bootstrap::registerClass('LoginLogPeer',      PATH_HOME . "engine/classes/model/LoginLogPeer.php");

  //Bootstrap::LoadSystem( 'pmException' );

// Create headPublisher singleton
//Bootstrap::LoadSystem( 'headPublisher' );
$oHeadPublisher = & headPublisher::getSingleton();

// Installer, redirect to install if we don't have a valid shared data folder
if (! defined( 'PATH_DATA' ) || ! file_exists( PATH_DATA )) {
    // new installer, extjs based
    define( 'PATH_DATA', PATH_C );
    require_once (PATH_CONTROLLERS . 'installer.php');
    $controller = 'Installer';

    // if the method name is empty set default to index method
    if (strpos( SYS_TARGET, '/' ) !== false) {
        list ($controller, $controllerAction) = explode( '/', SYS_TARGET );
    } else {
        $controllerAction = SYS_TARGET;
    }

    $controllerAction = ($controllerAction != '' && $controllerAction != 'login') ? $controllerAction : 'index';

    // create the installer controller and call its method
    if (is_callable( Array ('Installer',$controllerAction
    ) )) {
        $installer = new $controller();
        $installer->setHttpRequestData( $_REQUEST );
        $installer->call( $controllerAction );
    } else {
        $_SESSION['phpFileNotFound'] = $_SERVER['REQUEST_URI'];
        header( "location: /errors/error404.php?url=" . urlencode( $_SERVER['REQUEST_URI'] ) );
    }
    die();
}

// Load Language Translation
Bootstrap::LoadTranslationObject( defined( 'SYS_LANG' ) ? SYS_LANG : "en" );

// look for a disabled workspace
if ($oServerConf->isWSDisabled( SYS_TEMP )) {
    $aMessage['MESSAGE'] = Bootstrap::LoadTranslation( 'ID_DISB_WORKSPACE' );
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    Bootstrap::RenderPage( 'publish' );
    die();
}

// database and workspace definition
// if SYS_TEMP exists, the URL has a workspace, now we need to verify if exists their db.php file
if (defined( 'SYS_TEMP' ) && SYS_TEMP != '') {
    //this is the default, the workspace db.php file is in /shared/workflow/sites/SYS_SYS
    if (file_exists( PATH_DB . SYS_TEMP . '/db.php' )) {
        require_once (PATH_DB . SYS_TEMP . '/db.php');
        define( 'SYS_SYS', SYS_TEMP );

        // defining constant for workspace shared directory
        define( 'PATH_WORKSPACE', PATH_DB . SYS_SYS . PATH_SEP );
        // including workspace shared classes -> particularlly for pmTables
        set_include_path( get_include_path() . PATH_SEPARATOR . PATH_WORKSPACE );
    } else {
        Bootstrap::SendTemporalMessage( 'ID_NOT_WORKSPACE', "error" );
        Bootstrap::header( 'location: /sys/' . SYS_LANG . '/' . SYS_SKIN . '/main/sysLogin?errno=2' );
        die();
    }
} else { //when we are in global pages, outside any valid workspace
    if (SYS_TARGET === 'newSite') {
        $phpFile = G::ExpandPath( 'methods' ) . SYS_COLLECTION . "/" . SYS_TARGET . '.php';
        require_once ($phpFile);
        die();
    } else {
        if (SYS_TARGET == "dbInfo") { //Show dbInfo when no SYS_SYS
            require_once (PATH_METHODS . "login/dbInfo.php");
        } else {

            if (substr( SYS_SKIN, 0, 2 ) === 'ux' && SYS_TARGET != 'sysLoginVerify') { // new ux sysLogin - extjs based form
                require_once PATH_CONTROLLERS . 'main.php';
                $controllerClass = 'Main';
                $controllerAction = SYS_TARGET == 'sysLoginVerify' ? SYS_TARGET : 'sysLogin';
                //if the method exists
                if (is_callable( Array ($controllerClass,$controllerAction
                ) )) {
                    $controller = new $controllerClass();
                    $controller->setHttpRequestData( $_REQUEST );
                    $controller->call( $controllerAction );
                }
            } else { // classic sysLogin interface
                require_once (PATH_METHODS . "login/sysLogin.php");
                die();
            }
        }
        if (DEBUG_TIME_LOG)
            Bootstrap::logTimeByPage(); //log this page
        die();
    }
}

// PM Paths DATA
define( 'PATH_DATA_SITE', PATH_DATA . 'sites/' . SYS_SYS . '/' );
define( 'PATH_DOCUMENT', PATH_DATA_SITE . 'files/' );
define( 'PATH_DATA_MAILTEMPLATES', PATH_DATA_SITE . 'mailTemplates/' );
define( 'PATH_DATA_PUBLIC', PATH_DATA_SITE . 'public/' );
define( 'PATH_DATA_REPORTS', PATH_DATA_SITE . 'reports/' );
define( 'PATH_DYNAFORM', PATH_DATA_SITE . 'xmlForms/' );
define( 'PATH_IMAGES_ENVIRONMENT_FILES', PATH_DATA_SITE . 'usersFiles' . PATH_SEP );
define( 'PATH_IMAGES_ENVIRONMENT_USERS', PATH_DATA_SITE . 'usersPhotographies' . PATH_SEP );
define( 'SERVER_NAME', $_SERVER['SERVER_NAME'] );
define( 'SERVER_PORT', $_SERVER['SERVER_PORT'] );

// create memcached singleton
Bootstrap::LoadClass( 'memcached' );
$memcache = & PMmemcached::getSingleton( SYS_SYS );

// verify configuration for rest service
if ($isRestRequest) {
    // disable until confirm that rest is enabled & configured on rest-config.ini file
    $isRestRequest = false;
    $confFile = '';
    $restApiClassPath = '';

    // try load and getting rest configuration
    if (file_exists( PATH_DATA_SITE . 'rest-config.ini' )) {
        $confFile = PATH_DATA_SITE . 'rest-config.ini';
        $restApiClassPath = PATH_DATA_SITE;
    } elseif (file_exists( PATH_CONFIG . 'rest-config.ini' )) {
        $confFile = PATH_CONFIG . 'rest-config.ini';
    }
    if (! empty( $confFile ) && $restConfig = @parse_ini_file( $confFile, true )) {
        if (array_key_exists( 'enable_service', $restConfig )) {
            if ($restConfig['enable_service'] == 'true' || $restConfig['enable_service'] == '1') {
                $isRestRequest = true; // rest service enabled
            }
        }
    }
}

// load Plugins base class
Bootstrap::LoadClass( 'plugin' );

//here we are loading all plugins registered
//the singleton has a list of enabled plugins
$sSerializedFile = PATH_DATA_SITE . 'plugin.singleton';
$oPluginRegistry = & PMPluginRegistry::getSingleton();

if (file_exists( $sSerializedFile )) {
    $oPluginRegistry->unSerializeInstance( file_get_contents( $sSerializedFile ) );
}

// setup propel definitions and logging
//changed to autoloader
//require_once ("propel/Propel.php");
//require_once ("creole/Creole.php");

if (defined( 'DEBUG_SQL_LOG' ) && DEBUG_SQL_LOG) {
    define( 'PM_PID', mt_rand( 1, 999999 ) );
    require_once 'Log.php';

    // register debug connection decorator driver
    Creole::registerDriver( '*', 'creole.contrib.DebugConnection' );

    // initialize Propel with converted config file
    Propel::init( PATH_CORE . "config/databases.php" );

    // unified log file for all databases
    $logFile = PATH_DATA . 'log' . PATH_SEP . 'propel.log';
    $logger = Log::singleton( 'file', $logFile, 'wf ' . SYS_SYS, null, PEAR_LOG_INFO );
    Propel::setLogger( $logger );
    // log file for workflow database
    $con = Propel::getConnection( 'workflow' );
    if ($con instanceof DebugConnection) {
        $con->setLogger( $logger );
    }
    // log file for rbac database
    $con = Propel::getConnection( 'rbac' );

    if ($con instanceof DebugConnection) {
        $con->setLogger( $logger );
    }

    // log file for report database
    $con = Propel::getConnection( 'rp' );
    if ($con instanceof DebugConnection) {
        $con->setLogger( $logger );
    }
} else {
    Propel::init( PATH_CORE . "config/databases.php" );
}

Creole::registerDriver( 'dbarray', 'creole.contrib.DBArrayConnection' );

// Session Initializations
ini_set( 'session.auto_start', '1' );

// The register_globals feature has been DEPRECATED as of PHP 5.3.0. default value Off.
// ini_set( 'register_globals', 'Off' );
//session_start();
ob_start();

// Rebuild the base Workflow translations if not exists
if (! is_file( PATH_LANGUAGECONT . 'translation.en' )) {
    require_once ("classes/model/Translation.php");
    $fields = Translation::generateFileTranslation( 'en' );
}

// TODO: Verify if the language set into url is defined in translations env.
if (SYS_LANG != 'en' && ! is_file( PATH_LANGUAGECONT . 'translation.' . SYS_LANG )) {
    require_once ("classes/model/Translation.php");
    $fields = Translation::generateFileTranslation( SYS_LANG );
}

// Setup plugins
$oPluginRegistry->setupPlugins(); //get and setup enabled plugins
$avoidChangedWorkspaceValidation = false;

// Load custom Classes and Model from Plugins.
Bootstrap::LoadAllPluginModelClasses();

// jump to php file in methods directory
$collectionPlugin = '';
if ($oPluginRegistry->isRegisteredFolder( SYS_COLLECTION )) {
    $phpFile = PATH_PLUGINS . SYS_COLLECTION . PATH_SEP . SYS_TARGET . '.php';
    $targetPlugin = explode( '/', SYS_TARGET );
    $collectionPlugin = $targetPlugin[0];
    $avoidChangedWorkspaceValidation = true;
} else {
    $phpFile = Bootstrap::ExpandPath( 'methods' ) . SYS_COLLECTION . PATH_SEP . SYS_TARGET . '.php';
}

// services is a special folder,
if (SYS_COLLECTION == 'services') {
    $avoidChangedWorkspaceValidation = true;
    $targetPlugin = explode( '/', SYS_TARGET );

    if ($targetPlugin[0] == 'webdav') {
        $phpFile = Bootstrap::ExpandPath( 'methods' ) . SYS_COLLECTION . PATH_SEP . 'webdav.php';
    }
}

if (SYS_COLLECTION == 'login' && SYS_TARGET == 'login') {
    $avoidChangedWorkspaceValidation = true;
}

$bWE = false;
$isControllerCall = false;
if (substr( SYS_COLLECTION, 0, 8 ) === 'gulliver') {
    $phpFile = PATH_GULLIVER_HOME . 'methods/' . substr( SYS_COLLECTION, 8 ) . SYS_TARGET . '.php';
} else {
    //when the file is part of the public directory of any PROCESS, this a ProcessMaker feature
    if (preg_match( '/^[0-9][[:alnum:]]+$/', SYS_COLLECTION ) == 1) { //the pattern is /sysSYS/LANG/SKIN/PRO_UID/file
        $auxPart = explode( '/', $_SERVER['REQUEST_URI'] );
        $aAux = explode( '?', $auxPart[count( $auxPart ) - 1] );
        //$extPart = explode ( '.' , $auxPart[ count($auxPart)-1] );
        $extPart = explode( '.', $aAux[0] );
        $queryPart = isset( $aAux[1] ) ? $aAux[1] : "";
        $extension = $extPart[count( $extPart ) - 1];
        $phpFile = PATH_DATA_SITE . 'public' . PATH_SEP . SYS_COLLECTION . PATH_SEP . urldecode( $auxPart[count( $auxPart ) - 1] );
        $aAux = explode( '?', $phpFile );
        $phpFile = $aAux[0];

        if ($extension != 'php') {
            Bootstrap::streamFile( $phpFile );
            die();
        }

        $avoidChangedWorkspaceValidation = true;
        $bWE = true;
        //$phpFile = PATH_DATA_SITE . 'public' . PATH_SEP .  SYS_COLLECTION . PATH_SEP . $auxPart[ count($auxPart)-1];
    }

    //erik: verify if it is a Controller Class or httpProxyController Class
    if (is_file( PATH_CONTROLLERS . SYS_COLLECTION . '.php' )) {
        Bootstrap::LoadSystem( 'controller' );
        require_once PATH_CONTROLLERS . SYS_COLLECTION . '.php';
        $controllerClass = SYS_COLLECTION;
        //if the method name is empty set default to index method
        $controllerAction = SYS_TARGET != '' ? SYS_TARGET : 'index';
        //if the method exists
        if (is_callable( Array ($controllerClass,$controllerAction ) )) {
            $isControllerCall = true;
        }
    }
    if (! $isControllerCall && ! file_exists( $phpFile ) && ! $isRestRequest) {
        $_SESSION['phpFileNotFound'] = $_SERVER['REQUEST_URI'];
        header( "location: /errors/error404.php?url=" . urlencode( $_SERVER['REQUEST_URI'] ) );
        die();
    }
}

//redirect to login, if user changed the workspace in the URL
if (! $avoidChangedWorkspaceValidation && isset( $_SESSION['WORKSPACE'] ) && $_SESSION['WORKSPACE'] != SYS_SYS) {
    $_SESSION['WORKSPACE'] = SYS_SYS;
    Bootstrap::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', "error" );
    // verify if the current skin is a 'ux' variant
    $urlPart = substr( SYS_SKIN, 0, 2 ) == 'ux' && SYS_SKIN != 'uxs' ? '/main/login' : '/login/login';

    header( 'Location: /sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . $urlPart );
    die();
}

// enable rbac
Bootstrap::LoadSystem( 'rbac' );
$RBAC = &RBAC::getSingleton( PATH_DATA, session_id() );
$RBAC->sSystem = 'PROCESSMAKER';

// define and send Headers for all pages
if (! defined( 'EXECUTE_BY_CRON' )) {
    header( "Expires: " . gmdate( "D, d M Y H:i:s", mktime( 0, 0, 0, date( 'm' ), date( 'd' ) - 1, date( 'Y' ) ) ) . " GMT" );
    header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
    header( "Cache-Control: no-store, no-cache, must-revalidate" );
    header( "Cache-Control: post-check=0, pre-check=0", false );
    header( "Pragma: no-cache" );

    // get the language direction from ServerConf
    define( 'SYS_LANG_DIRECTION', $oServerConf->getLanDirection() );

    if ((isset( $_SESSION['USER_LOGGED'] )) && (! (isset( $_GET['sid'] )))) {
        $RBAC->initRBAC();
        //using optimization with memcache, the user data will be in memcache 8 hours, or until session id goes invalid
        $memKey = 'rbacSession' . session_id();
        if (($RBAC->aUserInfo = $memcache->get( $memKey )) === false) {
            $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
            $memcache->set( $memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS );
        }
    } else {
        // this is the blank list to allow execute scripts with no login (without session started)
        $noLoginFiles = $noLoginFolders = array ();
        $noLoginFiles[] = 'login';
        $noLoginFiles[] = 'authentication';
        $noLoginFiles[] = 'login_Ajax';
        $noLoginFiles[] = 'dbInfo';
        $noLoginFiles[] = 'sysLoginVerify';
        $noLoginFiles[] = 'processes_Ajax';
        $noLoginFiles[] = 'updateTranslation';
        $noLoginFiles[] = 'autoinstallProcesses';
        $noLoginFiles[] = 'autoinstallPlugins';
        $noLoginFiles[] = 'heartbeatStatus';
        $noLoginFiles[] = 'showLogoFile';
        $noLoginFiles[] = 'forgotPassword';
        $noLoginFiles[] = 'retrivePassword';
        $noLoginFiles[] = 'defaultAjaxDynaform';
        $noLoginFiles[] = 'dynaforms_checkDependentFields';

        $noLoginFolders[] = 'services';
        $noLoginFolders[] = 'tracker';
        $noLoginFolders[] = 'installer';

        // This sentence is used when you lost the Session
        if (! in_array( SYS_TARGET, $noLoginFiles ) && ! in_array( SYS_COLLECTION, $noLoginFolders ) && $bWE != true && $collectionPlugin != 'services' && ! $isRestRequest) {
            $bRedirect = true;
            if (isset( $_GET['sid'] )) {
                Bootstrap::LoadClass( 'sessions' );
                $oSessions = new Sessions();
                if ($aSession = $oSessions->verifySession( $_GET['sid'] )) {
                    require_once 'classes/model/Users.php';
                    $oUser = new Users();
                    $aUser = $oUser->load( $aSession['USR_UID'] );
                    $_SESSION['USER_LOGGED'] = $aUser['USR_UID'];
                    $_SESSION['USR_USERNAME'] = $aUser['USR_USERNAME'];
                    $bRedirect = false;
                    $RBAC->initRBAC();
                    $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
                    $memKey = 'rbacSession' . session_id();
                    $memcache->set( $memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS );
                }
            }

            if ($bRedirect) {
                if (substr( SYS_SKIN, 0, 2 ) == 'ux' && SYS_SKIN != 'uxs') { // verify if the current skin is a 'ux' variant
                    $loginUrl = 'main/login';
                } else if (strpos( $_SERVER['REQUEST_URI'], '/home' ) !== false) { //verify is it is using the uxs skin for simplified interface
                    $loginUrl = 'home/login';
                } else {
                    $loginUrl = 'login/login'; // just set up the classic login
                }

                if (empty( $_POST )) {
                    header( 'location: ' . SYS_URI . $loginUrl . '?u=' . urlencode( $_SERVER['REQUEST_URI'] ) );

                } else {
                    if ($isControllerCall) {
                        header( "HTTP/1.0 302 session lost in controller" );
                    } else {
                        header( 'location: ' . SYS_URI . $loginUrl );
                    }
                }
                die();
            }
        }
    }
    $_SESSION['phpLastFileFound'] = $_SERVER['REQUEST_URI'];

    /**
     * New feature for Gulliver framework to support Controllers & HttpProxyController classes handling
     *
     * @author <erik@colosa.com
     */
    if ($isControllerCall) { //Instance the Controller object and call the request method
        $controller = new $controllerClass();
        $controller->setHttpRequestData( $_REQUEST );
        $controller->call( $controllerAction );
    } elseif ($isRestRequest) {
        Bootstrap::dispatchRestService( SYS_TARGET, $restConfig, $restApiClassPath );
    } else {
        require_once $phpFile;
    }

    if (defined( 'SKIP_HEADERS' )) {
        header( "Expires: " . gmdate( "D, d M Y H:i:s", mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 1 ) ) . " GMT" );
        header( 'Cache-Control: public' );
        header( 'Pragma: ' );
    }

    ob_end_flush();
    if (DEBUG_TIME_LOG) {
        bootstrap::logTimeByPage(); //log this page
    }
}