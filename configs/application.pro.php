<?php
$config = array();
$config['phpsettings']['display_startup_errors'] = "0";
$config['phpsettings']['display_errors'] = "0";
$config['resources']['frontController']['throwExceptions'] = "0";
$config['resources']['frontController']['baseUrl'] = "/nextcms/index.php";
$config['resources']['Core_Application_Resource_Modules']['modules'] = "ad,category,comment,content,core,file,media,menu,message,poll,seo,tag,util,vote";
$config['install']['language'] = "en_US";
$config['db']['adapter'] = "mysql";
$config['db']['prefix'] = "";
$config['db']['master']['server2']['host'] = "localhost";
$config['db']['master']['server2']['port'] = "3306";
$config['db']['master']['server2']['dbname'] = "nextcms_1_0_0";
$config['db']['master']['server2']['username'] = "root";
$config['db']['master']['server2']['password'] = "123456";
$config['db']['slave']['server1']['host'] = "localhost";
$config['db']['slave']['server1']['port'] = "3306";
$config['db']['slave']['server1']['dbname'] = "nextcms_1_0_0";
$config['db']['slave']['server1']['username'] = "root";
$config['db']['slave']['server1']['password'] = "123456";

return $config;