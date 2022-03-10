<?php

/*
  Plugin Name: Ashad Family Tree
  Version: 1.0
  Author: Mohammed Ashad
  Author URI: https://elabins.com
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'inc/generatePet.php';

class AshadFamilyTreePlugin {
  function __construct() {
    global $wpdb;
    $this->charset = $wpdb->get_charset_collate();
    $this->tablename = $wpdb->prefix . 'ashad_members';
    add_action('activate_ashad-family-tree/ashad-family-tree.php', array($this, 'onActivate'));
    add_action('admin_head', array($this, 'onAdminRefresh'));
    add_action('wp_enqueue_scripts', array($this, 'loadAssets'));
    add_filter('template_include', array($this, 'loadTemplate'), 99);
  }

  function onActivate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $this->tablename {
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      familyid bigint(20) NOT NULL DEFAULT 0,
      fid bigint(20) NOT NULL DEFAULT 0,
      mid bigint(20) NOT NULL DEFAULT 0,
      pids bigint(20) NOT NULL DEFAULT 0,
      gender varchar(10) NOT NULL DEFAULT '',
      photo varchar (60),
      name varchar(60) NOT NULL DEFAULT '',
      born DATE,
      PRIMARY KEY  (id)
    } $this->charset;");
  }

  function onAdminRefresh() {
    
  }

  function loadAssets() {
    if (is_page('pet-adoption')) {
      wp_enqueue_style('petadoptioncss', plugin_dir_url(__FILE__) . 'pet-adoption.css');
    }
  }

  function loadTemplate($template) {
    if (is_page('pet-adoption')) {
      return plugin_dir_path(__FILE__) . 'inc/template-pets.php';
    }
    return $template;
  }

  function populateFast() {
    $query = "INSERT INTO $this->tablename (`species`, `birthyear`, `petweight`, `favfood`, `favhobby`, `favcolor`, `petname`) VALUES ";
    $numberofpets = 100000;
    for ($i = 0; $i < $numberofpets; $i++) {
      $pet = generatePet();
      $query .= "('{$pet['species']}', {$pet['birthyear']}, {$pet['petweight']}, '{$pet['favfood']}', '{$pet['favhobby']}', '{$pet['favcolor']}', '{$pet['petname']}')";
      if ($i != $numberofpets - 1) {
        $query .= ", ";
      }
    }
    /*
    Never use query directly like this without using $wpdb->prepare in the
    real world. I'm only using it this way here because the values I'm 
    inserting are coming fromy my innocent pet generator function so I
    know they are not malicious, and I simply want this example script
    to execute as quickly as possible and not use too much memory.
    */
    global $wpdb;
    $wpdb->query($query);
  }

}

$ashadFamilyTreePlugin = new AshadFamilyTreePlugin();