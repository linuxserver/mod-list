<?php

class LsMods {

  private $url = 'https://linuxserver.github.io/docker-mods/mod-list.yml';
  public $yaml;
  public $mods;
  public $validmods;

  public function __construct() 
  {
    $this->yaml = $this->getList();
    $mods = [];
    foreach($this->yaml['mods'] as $modname => $mod) {
      if($mod['mod_count'] > 0) $mods[$modname] = $mod;
    }
    $this->mods = $mods;
    $this->validmods = array_keys($mods);
  }

  public function getList()
  {
    $yaml = file_get_contents($this->url);
    if (function_exists('yaml_parse')) {
      return yaml_parse($yaml);
    } else {
      require_once "./Spyc.php";
      return Spyc::YAMLLoadString($yaml);
    }
  }

  public function render($mod = null)
  {
    if ($mod === null) {
      return $this->modList();
    }
    if($mod === 'create') {
      return $this->create();
    }
    if(!in_array($mod, $this->validmods)) {
      // return 'Invalid mod selected';
      return $this->create();
    }
    return $this->displayMod($mod);
  }

  public function create()
  {
    echo '<a class="back-to-list" href=".">Back to list</a>';
    echo '
    <div class="title">Submitting a PR for a Mod to be added to the official LinuxServer.io repo</div>
    <div class="content">
    <ul>
      <li>Ask the team to create a new branch named <code>&lt;baseimagename&gt;-&lt;modname&gt;</code> in this repo. Baseimage should be the name of the image the mod will be applied to. The new branch will be based on the [template branch](https://github.com/linuxserver/docker-mods/tree/template).</li>
      <li>Fork the repo, checkout the template branch.</li>
      <li>Edit the <code>Dockerfile</code> for the mod. <code>Dockerfile.complex</code> is only an example and included for reference; it should be deleted when done.</li>
      <li>Inspect the <code>root</code> folder contents. Edit, add and remove as necessary.</li>
      <li>Edit this readme with pertinent info, delete these instructions.</li>
      <li>Finally edit the <code>travis.yml</code>. Customize the build branch, and the vars for <code>BASEIMAGE</code> and <code>MODNAME</code>.</li>
      <li>Submit PR against the branch created by the team.</li>
      <li>Make sure that the commits in the PR are squashed.</li>
      <li>Also make sure that the commit and PR titles are in the format of <code>&lt;imagename&gt;: &lt;modname&gt; &lt;very brief description like "initial release" or "update"&gt;</code>. Detailed description and further info should be provided in the body (ie. <code>code-server: python2 add python-pip</code>).</li>
    </ul></div>'; 
  }

  public function modList()
  {
    echo '<a class="add-mod" href="./?mod=create">Add a mod</a>';
    foreach($this->mods as $modname => $mod) {
      echo '
<a class="section" href="./?mod='.$modname.'">
<div class="modcount">'.$mod['mod_count'].'<span>Mods</span></div>
<h2>'.$modname.'</h2>
<div class="go"><svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"><path style="fill:#cfd4d8;" d="M5 3l3.057-3 11.943 12-11.943 12-3.057-3 9-9z"/></svg></div>
</a>';
    }
  }

  public function displayMod($displaymod)
  {
    echo '<a class="back-to-list" href=".">Back to list</a>';
    echo '<div class="title">'.$displaymod.'</div>';
    foreach($this->mods[$displaymod]['container_mods'] as $mod) {
      echo '
<a class="section" href="'.current($mod).'">
<div class="modcount"><svg width="40" height="40" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
<path style="fill: #dee4e8;" fill-rule="evenodd" clip-rule="evenodd" d="M512 0C229.12 0 0 229.12 0 512c0 226.56 146.56 417.92 350.08 485.76 25.6 4.48 35.2-10.88 35.2-24.32 0-12.16-.64-52.48-.64-95.36-128.64 23.68-161.92-31.36-172.16-60.16-5.76-14.72-30.72-60.16-52.48-72.32-17.92-9.6-43.52-33.28-.64-33.92 40.32-.64 69.12 37.12 78.72 52.48 46.08 77.44 119.68 55.68 149.12 42.24 4.48-33.28 17.92-55.68 32.64-68.48-113.92-12.8-232.96-56.96-232.96-252.8 0-55.68 19.84-101.76 52.48-137.6-5.12-12.8-23.04-65.28 5.12-135.68 0 0 42.88-13.44 140.8 52.48 40.96-11.52 84.48-17.28 128-17.28 43.52 0 87.04 5.76 128 17.28 97.92-66.56 140.8-52.48 140.8-52.48 28.16 70.4 10.24 122.88 5.12 135.68 32.64 35.84 52.48 81.28 52.48 137.6 0 196.48-119.68 240-233.6 252.8 18.56 16 34.56 46.72 34.56 94.72 0 68.48-.64 123.52-.64 140.8 0 13.44 9.6 29.44 35.2 24.32C877.44 929.92 1024 737.92 1024 512 1024 229.12 794.88 0 512 0z" fill="#1B1F23"/>
</svg></div>
<h2>'.key($mod).'</h2>
<div class="go"><svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"><path style="fill:#cfd4d8;" d="M5 3l3.057-3 11.943 12-11.943 12-3.057-3 9-9z"/></svg></div>
</a>';
    }
    echo '<a href="./?mod=create" class="add">Add a mod</a>';
  }
}

$lsmods = new LsMods;
?><!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Linuxserver Container Mods</title>
  <meta name="description" content="List of mods for Linuxserver.io containers">
  <meta name="author" content="linuxserver.io">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400&display=swap" rel="stylesheet">
  <style>
    body,html {
      padding: 0;
      margin: 0;
    }
    body {
      background: #e8e8e8;
      font-family: 'Lato', sans-serif;
      color: #738694;
      font-weight: 400;
      font-size: 16px;
    }
    body * {
      box-sizing: border-box;
    }
    code {
      font-family: 'Lato', sans-serif;
      font-size: 14px;
      background: #e8e8e8;
      padding: 2px 5px;
      border-radius: 3px;
    }
    #app {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 15px;
    }
    h1 {
      font-size: 30px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: #738694;
      margin: 60px 0 45px;
      font-weight: 400;
    }
    h1 span {
      color: #9bb0bf;
    }
    .section, .content {
      width: 100%;
      max-width: 600px;
      display: flex;
      align-items: center;
      background: #efefef;
      justify-content: space-between;
      text-decoration: none;
      margin: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .content {
      padding: 30px;
    }
    .content li {
      margin: 15px 0;
      line-height: 1.4;
    }
    .section h2 {
      margin: 0;
      flex: 1;
      padding: 0 30px;
      color: #738694;
      font-weight: 400;
      text-transform: uppercase;
      font-size: 18px;
    }
    .section .modcount {
      width: 80px;
      height: 80px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 30px;
      flex-direction: column;
      background: #3e81b9;
      color: #fff;
    }
    .section .modcount span {
      font-size: 12px;
      color: #ffffffa8;
      text-transform: uppercase;
    }
    .section .go {
      width: 80px;
      height: 80px;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .back-to-list, .add-mod {
      position: fixed;
      left: 0;
      padding: 15px;
      background: #3eb99d;
      color: #fff;
      top: 50%;
      transform: rotate(270deg) translateZ(0);
      transform-origin: 25px 25px;
      text-decoration: none;
      font-weight: 400;
    }
    .add-mod {
      background: #b93e93;
    }
    .title {
      font-size: 25px;
      padding: 20px;
      text-align: center;
      text-transform: uppercase;
      background: #e4e4e4;
      margin-bottom: 25px;
      width: 100%;
      max-width: 600px;

    }
    .add {
      width: 100%;
      max-width: 600px;
      display: flex;
      justify-content: center;
      text-decoration: none;
      margin: 10px;
      border: 5px dashed #ccc;
      font-size: 25px;
      padding: 20px;
      color: #ccc;
      font-weight: 400;
      text-transform: uppercase;
    }
    @media only screen and (min-width: 500px) {
      h1 {
        font-size: 50px;
        letter-spacing: 5px;

      }
    }

  </style>

</head>
<body>
    <div id="app">
      <header>
        <h1>Linux<span>Server</span>.io</h1>
      </header>
    <?php 
      echo $lsmods->render($_GET['mod'] ?? null);
    ?>
    </div>
</body>
</html>