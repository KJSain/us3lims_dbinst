<?php
/*
 * cluster_info.php
 *
 * Information about clusters that programs might want to know
 *
 */

$clusters = array();   // Information about the clusters

$clusters[ 'bcf' ] = array 
(
  "name"       => "bcf.uthscsa.edu",
  "submittype" => "local",
  "submithost" => "",
  "httpport"   => 0,
  "sshport"    => 22,
  "workdir"    => "/home/us3/work/"   // Need trailing slash
);

$clusters[ 'alamo' ] = array 
(
  "name"       => "alamo.uthscsa.edu",
  "submittype" => "local",
  "submithost" => "",
  "httpport"   => 0,
  "sshport"    => 22,
  "workdir"    => "/home/us3/work/"   // Need trailing slash
);

$clusters[ 'laredo' ] = array 
(
  "name"       => "alamo.uthscsa.edu",
  "submittype" => "local",
  "submithost" => "",
  "httpport"   => 0,
  "sshport"    => 22,
  "workdir"    => "/home/us3/work/"    // Need trailing slash
);

$clusters[ 'ranger' ] = array 
(
  "name"       => "ranger.tacc.teragrid.org",
  "submittype" => "http",
  "submithost" => "http://gw33.quarry.iu.teragrid.org",
  "httpport"   => 8080,
  "sshport"    => 22,
  "workdir"    => "/ogce-rest/job" 
);

$clusters[ 'lonestar' ] = array 
(
  "name"       => "lonestar.tacc.teragrid.org",
  "submittype" => "http",
  "submithost" => "http://gw33.quarry.iu.teragrid.org",
  "httpport"   => 8080,
  "sshport"    => 22,
  "workdir"    => "/ogce-rest/job" 
);

$clusters[ 'queenbee' ] = array 
(
  "name"       => "queenbee.loni-lsu.teragrid.org",
  "submittype" => "http",
  "submithost" => "http://gw33.quarry.iu.teragrid.org",
  "httpport"   => 8080,
  "sshport"    => 22,
  "workdir"    => "/ogce-rest/job/" 
);
