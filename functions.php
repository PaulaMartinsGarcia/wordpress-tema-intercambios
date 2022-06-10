<?php

function registrandoTaxonomia(){
  register_taxonomy(
    'paises',
    'destinos',
    array(
      'labels' => array('name' => 'Países'),
      'hierarchical' => true
    )
    );
}
add_action('init', 'registrandoTaxonomia');

function postCustomizado(){
  register_post_type('destinos', 
  array(
    'labels' => array('name' => 'Destinos'),
    'public' => true,
    'menu_position' => 0,
    'supports' => array('title', 'editor', 'thumbnail'),
    'menu_icon' => 'dashicons-admin-site'
  )
  );
}
add_action('init', 'postCustomizado');

function adicionarRecursos(){
  add_theme_support('custom-logo');
  add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'adicionarRecursos');

function menu(){
  register_nav_menu(
    'menu-navegacao',
    'Menu navegacao'
  );
}

add_action('init', 'menu');

function banner(){
  register_post_type(
    'banners',
    array(
      'labels' => array('name' => 'Banner'),
      'public' => true,
      'menu_position' => 1,
      'menu_icon' => 'dashicons-format-image',
      'supports' => array('title', 'thumbnail')
    )
    );
}
add_action('init', 'banner');

function metabox(){
  add_meta_box(
    'ai_registrando_metabox',
    'Texto para a home',
    'ai_funcao_callback',
    'banners'
  );
}
add_action('add_meta_boxes', 'metabox');

function ai_funcao_callback($post){
  $texto_home_1 = get_post_meta($post->ID, '_texto_home_1', true);
  $texto_home_2 = get_post_meta($post->ID, '_texto_home_2', true);
  ?>
  <label for="texto_home_1">Texto 1</label>
  <input type="text" name="texto_home_1" style="..." value="<?= $texto_home_1 ?>"/>
  <br>
  <br>
  <label for="texto_home_2">Texto 2</label>
  <input type="text" name="texto_home_2" style="..." value="<?= $texto_home_1 ?>"/>
  <?php
}

function salvandoDadosMetabox($post_id){
    foreach( $_POST as $key=>$value){
      if($key !== 'texto_home_1' && $key !== 'texto_home_2'){
        continue;
      }
      update_post_meta(
        $post_id,
        '_' . $key,
        $_POST[$key]
      );
    }
}
add_action('save_post', 'salvandoDadosMetabox');

function pegandoTextosParaBanner(){

      $args = array(
          'post_type' => 'banners',
          'post_status' => 'publish',
          'posts_per_page' => 1
      );

      $query = new WP_Query($args);
      if($query->have_posts()):
          while($query->have_posts()): $query->the_post();
              $texto1 = get_post_meta(get_the_ID(), '_texto_home_1', true);
              $texto2 = get_post_meta(get_the_ID(), '_texto_home_2', true);
              return array(
                  'texto_1' => $texto1,
                  'texto_2' => $texto2
              );
          endwhile;
      endif;  
}

function adicionandoScripts(){

    $textosBanner = pegandoTextosParaBanner();

    if(is_front_page()){
      wp_enqueue_script('typed-js', get_template_directory_uri() . '/js/typed.min.js', array(), false, true);
      wp_enqueue_script('texto-banner-js', get_template_directory_uri() . '/js/texto-banner.js', array('typed-js'), false, true);
      wp_localize_script('texto-banner-js', 'data',  $textosBanner);
    }
}
add_action('wp_enqueue_scripts', 'adicionandoScripts');