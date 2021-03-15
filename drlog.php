<?php
 
/*
Plugin Name: Developer Relief Logger
Plugin URI: https://devrelief.net/drlog
Description: Plugin to to help with logging during development
Version: 1.0
Author: Fred Christianson
Author URI: https://devrelief.new/fredchristianson
License: The Unlicense
 
*/
namespace DRLog;

define('DRLOG_VERSION', "0.0.1");


class LogWriter {
    private $loggers = array();
    private $messages = array();
    private static $singleton = null;

    public static function instance() {
        if (self::$singleton == null) {
            self::$singleton = new LogWriter();
        }
        return self::$singleton;
    }
    public function __constructor() {
        $this->singleton = $this;
    }

    function drlog_plugin_assets() {
        wp_register_style('drlog_style', plugin_dir_url( __FILE__ ).'/css/drlog.css?v='.DRLOG_VERSION, __FILE__);
        wp_enqueue_style('drlog_style');
        
        wp_enqueue_script( 'drlog-javascript',plugin_dir_url( __FILE__ ).( '/js/drlog.js?v='.DRLOG_VERSION), __FILE__);
    
    }

    function drlog_message_writer($content) {
        echo '<div class="drlog-messages">';
        echo '<h1>DevRelief Log Messages</h1>';
        foreach($this->messages as $message) {
            echo '<div>'.$message.'</div>';
        }
        echo '</div>';
    }

    function addLogger($logger) {
        array_push($this->loggers,$logger);
    }
    
    function addMessage($message) {
        array_push($this->messages,$message);
    }
}


$logWriter = LogWriter::instance();
add_action( 'wp_enqueue_scripts', array($logWriter,'drlog_plugin_assets') );
add_action('wp_footer', array($logWriter,'drlog_message_writer'));

class DRLogger {
    private $moduleName = 'unnamed';
    public function __construct($moduleName) {
        $this->moduleName = $moduleName;
        $this->logWriter = LogWriter::instance();
        $this->logWriter->addLogger($this);
    }

    function write($message) {
        $this->logWriter->addMessage($message);

    }

    public function debug($message) {
        $this->write($message);
    }
}