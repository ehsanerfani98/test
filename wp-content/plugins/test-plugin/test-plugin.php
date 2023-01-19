<?php

/**
 * Plugin Name: test
 * Plugin URI:  test
 * Description: test
 * Version:     1.0
 * Author:      test
 * Author URI:  test
 */

add_action('init', 'setup_plugin');
function setup_plugin()
{
   add_action('admin_menu', function () {
      add_menu_page('test api', 'test api', 'manage_options', 'test-api', 'fun_testapi');
   });

   function fun_testapi()
   {
?>
      <style>
         .wrap-form {
            margin-top: 1rem;
            padding: 1rem;
         }

         .input-wrap {
            margin-bottom: 1rem;
         }
      </style>
      <div class="wrap-form">
         <form action="" method="post">
            <div class="input-wrap">
               <label for="">Register Api Key</label>
               <input type="text" name="api_key" value="<?= get_option('api_key') ?>">
            </div>
            <div class="input-wrap">
               <label for="">Enable Catch</label>
               <input type="checkbox" name="enable_catch" value="1" <?= get_option('enable_catch') ? 'checked' : '' ?>>
            </div>
            <div class="input-wrap">

               <button type="submit" name="btn_api">submit</button>
            </div>


         </form>
         <form action="" method="post">

            <div class="input-wrap">
               <button type="submit" name="remove_catch">Remove Catch</button>
            </div>


         </form>
      </div>

   <?php
   }
}


if (isset($_POST['remove_catch'])) {
   update_option('catch_data_foods', '');
}

if (isset($_POST['btn_api'])) {
   $add_key = update_option('api_key', $_POST['api_key']);
   if (isset($_POST['enable_catch'])) {
      update_option('enable_catch', $_POST['enable_catch']);
   } else {
      update_option('enable_catch', 0);
   }
   // if($add_key){
   //    echo "success";
   // }
}



add_shortcode('test-api', 'view_sc_api');
function view_sc_api()
{
   $api_key = get_option('api_key');

   if (!get_option('enable_catch')) {


      $parameter = [
         'number' => 10
      ];
      $query = 'https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/random/?' . http_build_query($parameter);
      $request = wp_remote_get($query, [
         'headers' => [
            'Accept' => 'application/json',
            'X-RapidAPI-Key' => $api_key
         ]
      ]);


      $body = wp_remote_retrieve_body($request);
      $data = json_decode($body, true);
   } else {

      if (empty(get_option('catch_data_foods'))) {
         $parameter = [
            'number' => 10
         ];
         $query = 'https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/random/?' . http_build_query($parameter);
         $request = wp_remote_get($query, [
            'headers' => [
               'Accept' => 'application/json',
               'X-RapidAPI-Key' => $api_key
            ]
         ]);

         $body = wp_remote_retrieve_body($request);
         $data = json_decode($body, true);
         update_option('catch_data_foods', $data);
      } else {
         $data = get_option('catch_data_foods');
      }
   }
   ?>
   <style>
      .card {
         border-radius: 10px;
         padding: 1rem;
         border: 1px solid #ccc;
         background: white;
         box-shadow: 0px 4px 10px #ccc;
         margin: 1rem 0;
      }

      .title-card {
         background: lightgreen;
         padding: 1rem;
         border-radius: 6px;
         margin-bottom: 10px;
      }

      .content-card {
         padding: 1rem;
      }

      .wrapper {
         width: 1200px;
         margin: 0 auto;
      }
   </style>
   <div class="wrapper">
      <?php
      if (!isset($_GET['food_id'])) {
         foreach ($data['recipes'] as $item) {
      ?>
            <a href="<?= home_url('testapi') . '?food_id=' . $item['id'] ?>">
               <div class="card">
                  <div class="title-card"><?= $item['title'] ?></div>
                  <div class="content-card"><?= $item['summary'] ?></div>
               </div>
            </a>
         <?php
         }
      } else {


         $query = 'https://spoonacular-recipe-food-nutrition-v1.p.rapidapi.com/recipes/' . $_GET['food_id'] . '/summary';
         $request = wp_remote_get($query, [
            'headers' => [
               'Accept' => 'application/json',
               'X-RapidAPI-Key' => $api_key
            ]
         ]);

         $body = wp_remote_retrieve_body($request);
         $data = json_decode($body, true);

         ?>
         <div class="card">
            <div class="title-card"><?= $data['title'] ?></div>
            <div class="content-card"><?= $data['summary'] ?></div>
         </div>
      <?php
      }
      ?>
   </div>
<?php
}
