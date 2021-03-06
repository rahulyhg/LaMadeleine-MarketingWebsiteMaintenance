<?php

/**
*
* La Madeleine custom theme functions
*
**/

function required_lam_widgets_init() {
	// unregister parent theme sidebars
	unregister_sidebar( 'sidebar-main' );
	unregister_sidebar( 'sidebar-footer-1' );
	unregister_sidebar( 'sidebar-footer-2' );
	unregister_sidebar( 'sidebar-footer-3' );
	
	// reregister and insert ours in the desired order.

	register_sidebar( array(
		'name' => __( 'Home Sidebar', 'requiredfoundation' ),
		'id' => 'sidebar-home',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title"><span>',
		'after_title' => '</span></h4>',
	) );

	register_sidebar( array(
		'name' => __( 'Menu Sidebar', 'requiredfoundation' ),
		'id' => 'sidebar-menu',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title"><span>',
		'after_title' => '</span></h4>',
	) );

	register_sidebar( array(
		'name' => __( 'Locations Sidebar', 'requiredfoundation' ),
		'id' => 'sidebar-location',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title"><span>',
		'after_title' => '</span></h4>',
	) );

	register_sidebar( array(
		'name' => __( 'Story Sidebar', 'requiredfoundation' ),
		'id' => 'sidebar-story',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title"><span>',
		'after_title' => '</span></h4>',
	) );
}

function required_lam_enqueue() {
    global $wp_styles;

	// Global stylesheet
	wp_enqueue_style( 'importer', get_stylesheet_directory_uri().'/library/styles/css/importer.css', array('foundation-css') );

	/*** IE Fixes ***/
	//wp_enqueue_style( 'IE-fixes', get_stylesheet_directory_uri().'/ie-fixes.css', false, false );
	//$wp_styles->add_data( 'IE-fixes', 'conditional', 'lt IE 9' );
	
	// change jQuery to Google Code version and move to footer.
	wp_dequeue_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js',array(),false,true);
	// latest jQuery UI
	// wp_enqueue_script('jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js', array('jquery'), false, true);
	// our plugins scripts for the theme
	wp_enqueue_script( 'required_lam_plugin', get_stylesheet_directory_uri().'/library/js/plugins.js', array('jquery'), false, true);
	// our primary scripts for the theme
	wp_enqueue_script( 'required_lam_main', get_stylesheet_directory_uri().'/library/js/main.js', array('jquery'), false, true);

	wp_enqueue_script( 'required_lam_location', get_stylesheet_directory_uri().'/library/js/la_mad_locations.js', array('jquery'), false, true);

	wp_enqueue_script( 'required_lam_cart', get_stylesheet_directory_uri().'/library/js/la_mad_cart.js', array('jquery'), false, true);
}

/**
*
* Actions
*
**/
add_action( 'widgets_init', 'required_lam_widgets_init', 15 );
add_action( 'wp_enqueue_scripts', 'required_lam_enqueue' );

/**
*
* Display next and previous stories
*
**/
function lam_single_content_nav( ) {
	?>
	<nav class="nav-single-stories">
		<h3 class="assistive-text"><?php _e( 'Post navigation', 'requiredfoundation' ); ?></h3>
		<span class="nav-previous"><?php previous_post_link( '%link', '<span class="icon icon-arrow-left-large"></span> %title' ); ?></span>
		<span class="nav-next"><?php next_post_link( '%link', '%title <span class="icon icon-arrow-right-large"></span>' ); ?></span>
	</nav><!-- .nav-single -->
	<?php
};

/**
*
* Custom Image Sizes
*
**/
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'fma-full', 820, 750, true ); // 820 pixels wide by 750 pixels tall, hard crop true
	add_image_size( 'daypart', 265, 95, true ); // 265 pixels wide by 95 pixels tall, hard crop true
	add_image_size( 'menu-featured', 820, 360, true ); // 820 pixels wide by 360 pixels tall, hard crop true
	add_image_size( 'menu-item-featured', 365, 200, true ); // 365 pixels wide by 200 pixels tall, hard crop true
	add_image_size( 'menu-item-featured-story', 365, 300, true ); // 365 pixels wide by 300 pixels tall, hard crop true
	add_image_size( 'location-featured', 270, 150, true ); // 270 pixels wide by 150 pixels tall, hard crop true
	add_image_size( 'featured-top', 820, 390, true ); // 820 pixels wide by 360 pixels tall, hard crop true
}

/**
*
* Strip out img height & width attribute
*
**/

add_filter( 'get_image_tag', 'remove_width_and_height_attribute', 10 );
add_filter( 'post_thumbnail_html', 'remove_width_and_height_attribute', 10 );

function remove_width_and_height_attribute( $html ) {
   return preg_replace( '/(height|width)="\d*"\s/', "", $html );
}

/**
*
* Add class to excerpts
*
**/
add_filter( "the_excerpt", "add_class_to_excerpt" );
function add_class_to_excerpt( $excerpt ) {
    return str_replace('<p', '<p class="excerpt"', $excerpt);
}

function custom_excerpt_length( $length ) {
	return 10;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
* Create Teaser
* Accepts string and length value, returns teaser
*/
function create_teaser($content,$length,$hellip=TRUE) {
	$teaser = '';
	$i=0; // array units
	$c=0; // character counter
	$para = explode(" ", $content);

	if ($length >= (strlen($content) - strlen(end($para)))) { // desired length is longer than the content minus the last word
		$teaser = $content;
		$hellip = FALSE;
	} else { // trim this puppy
		while ($c <= $length) {
			$teaser .= ($i==0 ? "" : " ") . $para[$i];
			$c = $c + strlen($para[$i]) + 1; // extra +1 is for the space we're appending
			$i++;
		}
	}

	// prettify -- if the last character just happens to be a period, remove it before appending the ellipsis
	$teaser = (substr($teaser,-1) == "." && $hellip==TRUE) ? substr($teaser,0,-1) : $teaser ;

	// add ellipsis if requested or necessary
	$teaser .= ($hellip==TRUE ? "&hellip;" : "");

	return $teaser;
}

/**
*
* Display featured menu item
* Accept featured menu item object, returns markup
*
**/
function display_featured_item($featuredObj){
	// This featured item object
  $featured = $featuredObj;

  // If the featured item object is populated
  if(count($featured) > 0) :

  		// Does this featured item have a story related? 
  		if(count($featured['story']) > 1) :
  			$hasStory = true;
  		endif;

      // Start featured item element
      $str = '<div class="featured-menu-item">';

      if( !empty($hasStory) ) :
	      // This featured item has a story. Use a larger image size to make room for story teaser. 
	      $str .= $featured['featured_img_story'];
	    	$featuredItemClass = "has-story";
	   	else :
	   		// No story, use standard image size
	   		$str .= $featured['featured_img'];
	   		$featuredItemClass = "no-story";
	   	endif;

      // Start text wrapper
      $str .= '<div class="text-wrapper ' . $featuredItemClass . '">';

      // Include featured item title
      $str .= '<p class="title">' . $featured['title'] . '</p>';

      // If there is a story associated with this featured item
      if( !empty($hasStory) ) :

          // This story
          $story = $featured['story'];

          // Story wrapper
          $str .= '<div class="story-wrapper">';

          // Add story icon
          $str .= '<div class="icon icon-stories"></div>';

          // Generate and include story teaser
          $str .= '<div class="story-teaser">' . create_teaser($story['post_content'], 65) . ' <a href="' . $story['guid'] . '">' . $story['call_to_action'] . '&nbsp;<span class="icon icon-arrow-right"></span></a></div>';

          // Close .story-wrapper
          $str .= '</div>';

      endif;

      // Close .text-wrapper and .featured-menu-item
      $str .= "</div></div>";

      return $str;

  endif;
}

/**
*
* Display menu item
* Accepts menu item object and featured item obj, returns markup
*
**/
function display_menu_item($menuItemObj, $featuredItemObj){
	$str = '';
	// Does this menu item  have a story related? 
	if(count($menuItemObj['story']) > 1) :
		$hasStory = true;
		// This story
		$story = $menuItemObj['story'];
	endif;

  // Count menu item keys
  $keyLength = count($menuItemObj['menu_key_relationship']);

  // Echo menu item wrapper
  $str .= '<div class="menu-item">';

  // Echo menu item title
  $str .= '<div class="title-wrapper"><p class="title">' . $menuItemObj['title'] . '</p>';

  // If there are menu keys assigned with this item display associated icons
  if($keyLength > 0) :
      $str .= '<div class="menu-keys">';
      // Iterate through menu item keys
        if((isset($menuItemObj['menu_key_relationship']))&& is_array($menuItemObj['menu_key_relationship'])){
          foreach($menuItemObj['menu_key_relationship'] as $menuKey){
              // Echo this menu item key
              $str .= '<span class="icon icon-legend-' . $menuKey['slug'] . '"></span>';
          }
        }
      $str .= '</div>';
  endif;

  // Closing .title-wrapper
  $str .= '</div>';

  // Echo menu item description
  $isPriced = '';
  if($menuItemObj['price_min'] == '0.00' || $menuItemObj['price_max'] == '0.00'):
  	$isPriced = "ispriced-false";
  endif;

  $str .= '<p class="desc">' . $menuItemObj['description'] . '<span class="pricing ' . $isPriced . '">
  <span class="min">$' . $menuItemObj['price_min'] . '</span>';
  if (!empty($menuItemObj['price_tier_2'])) {
  	$str .= '<span class="tier2">$' . $menuItemObj['price_tier_2'] . '</span>';
  }
  if (!empty($menuItemObj['price_tier_2'])) {
  	$str .= '<span class="tier3">$' . $menuItemObj['price_tier_3'] . '</span>';
  }
  $str .= '<span class="max">$' . $menuItemObj['price_max'] . '</span>
  </span></p>';

  $str .='<div class="optional_item">';
  for($i=1; $i<=5; $i++){
     if(  (isset($menuItemObj['description_'.$i])) && ( strlen($menuItemObj['description_'.$i]) >1) ){
        $isPriced = '';
        $str .= "<div class='pricing'>".$menuItemObj['description_'.$i];
        $str .= '<span class="pricing ' . $isPriced . '">
              	<span class="min">$' . $menuItemObj['optional_min_price_'.$i] . '</span>';
              	if (!empty($menuItemObj['optional_price_'.$i.'_tier_2'])) {
                	$str .= '<span class="tier2">$' . $menuItemObj['optional_price_'.$i.'_tier_2'] . '</span>';
                }
                if (!empty($menuItemObj['optional_price_'.$i.'_tier_3'])) {
                	$str .= '<span class="tier3">$' . $menuItemObj['optional_price_'.$i.'_tier_3'] . '</span>';
                }
                $str .= '<span class="max">$' . $menuItemObj['optional_max_price_'.$i] . '</span>
                </span>
                </div>';
     }
  }

  $str .='</div>';

  if( !empty($hasStory) )  :
  	// If this menu item is not also the featured item, display the story CTA under the menu item in the category
  	if($menuItemObj['title'] != $featuredItemObj['title']) :
  		// Generate and include story teaser
  		$str .= '<div class="story-teaser"><div class="icon icon-stories"></div><p>' . create_teaser($story['post_content'], 75) . ' <a href="' . $story['guid'] . '">' . $story['call_to_action'] . ' <span class="icon icon-arrow-right"></span></a></p></div>';
  	endif;
  endif;

  // Closing .menu-item
  $str .= '</div>';

  // Return menu item markup
  return $str;

}

/**
*
* Display menu category by item
* Accepts menu object, returns markup containing menu category and all menu items
*
**/
function display_menu_category($menuObj,$layout){

	if(count($menuObj > 0)) : 

		// If no layout is defined, default to left layout
		if(!$layout){
			$layout = 'left';
		}

		// This category's name
	  $menuCategory = $menuObj['items'][0]['menu_category'];

	  // Is there a featured menu item? 
	  if(count($menuObj['featured'])) :
	  	// Yes! 
	  	$isFeatured = true;
	  	$featured = $menuObj['featured'];
	  endif; 

	  // This category's total number of menu items
	  $totalMenuItems = count($menuObj['items']);

	  // Determine where to split columns
	  if($isFeatured && $totalMenuItems > 2) :

	  	// Featured item and more than two items, stop one item short to better balance column against featured item image

	  	// Special rules for VIENNOISERIE and PÂTISSERIE - Split difference is higher for better balance
			if($menuCategory['term_id'] == 23 || $menuCategory['term_id'] == 24) :
				$splitDifference = 3;
			// Else if SPECIALTIES, set split difference only slightly higher
			elseif($menuCategory['term_id'] == 26) :
				$splitDifference = 2;
			else :
				// Else use standard 1 split difference
				$splitDifference = 1;
  		endif;

			if($layout === 'left') :
	  		$splitItems = ceil(($totalMenuItems / 2) - $splitDifference);

	  	else :
	  		$splitItems = ceil(($totalMenuItems / 2) + $splitDifference);
	  	endif;
	  else : 
	  	// Less than two items or no featured item, split evenly in half
      // Serna: This needs to be a whole number
	  	$splitItems = ceil($totalMenuItems / 2);
	  endif; 

	  // String to return containing markup
	  $str = "";

	  // Start new menu category row

	  // Set a unqiue ID based on the category name
	  $uniqueID = strtolower($menuCategory['name']);
	  $uniqueID = str_replace(' ', '-', $uniqueID);

    /** this is hacky but aparently strtolower fails on accented letters **/
    $uniqueID = str_replace('Á', 'á', $uniqueID);

	  $str .= '<div class="row menu-category" id="category-' . $uniqueID . '">';

	  // Wrap in category title & description
	  $str .= '<div class="category-wrapper">';

	  // Include menu category name
	  $str .= '<h2 class="category-title">' . $menuCategory['name'] . '</h2>';

	  // Add menu category subhead if exists
	  if(count($menuCategory['description']) > 0) :
	  	$str .= '<p class="category-desc">' . $menuCategory['description'] . '</p>';
	  endif;

	  // Close category wrapper
	  $str .= '</div>';

	  // Start first column
	  $str .= '<div class="six columns">';

	  // If 'left' layout, include featured menu item at top of left column
	  if($isFeatured && $layout === 'left') :
	  	// Display featured menu item
	  	$str .= display_featured_item($featured);
	  endif; 
	      
	  // Iterate through first half of total menu items and populate first column
	  for($i = 0; $i < $splitItems; $i++){ 

	    // Display menu item, pass menu item object and featured item object
	    $str .= display_menu_item($menuObj['items'][$i],$featured);

	  };
	          
	  $str .= '</div>';

	  if($totalMenuItems > 1) :

		  // Start second column
		  $str .= '<div class="six columns">';

			// If 'right' layout, include featured menu item at top of right column
		  if($isFeatured && $layout === 'right') :
		  	// Display featured menu item
		  	$str .= display_featured_item($featured);
		  endif; 
		          
		  // Iterate through second half of total menu items and populate second column
		  // Starting 1 item before half to better balance against featured item image
		  for($i = $splitItems; $i < $totalMenuItems; $i++){

		  	$str .= display_menu_item($menuObj['items'][$i],$featured);
		    
		  };
		          
			// Closing second column
			$str .= '</div>';

		endif;
	  
	  // Closing menu category row
	  $str .= '</div>';

	  return $str;

	endif;
};

/**
*
* Build and return stories carousel
* Accepts stories object, returns markup ready for slide.js to initialize
*
**/
function display_story_carousel($stories){

	// Iterate through array and remove any stories without a featured image
	foreach ($stories as $key => $story){
		if(strlen($story['fma_full']) == 0) :
			unset($stories[$key]);
		endif;
	}

	// Get story count
	$storyCount = count($stories);
	
	// If stories object contains data
	if($storyCount > 0){
		// Open carousel wrapper
		$str = '<div id="carousel" class="swipe">';

		// Open carousel swipe wrapper
		$str .= '<div class="swipe-wrap">';

		// Iterate through updated stories array
		foreach ($stories as $key => $story){

			// Open carousel item wrapper
			$str .= '<div class="carousel-item has-gradient-' . $story['has_gradient'] . '">';
			// Include image
			$str .= '<div>' . $story['fma_full'] . '</div>';
			// Open carousel text wrapper
			$str .= '<div class="carousel-text">';
			// Carousel header
			$str .= '<h1>' . $story['title'] . '</h1>';
			// Carousel subhead
			$str .= '<p class="subhead">' . $story['excerpt'] . '</p>';
			// Carousel call-to-action button
			$str .= '<p><a class="btn" href="' . $story['permalink'] . '">' . $story['call_to_action'] . '</a></p>';
			// Close carousel text wrapper and carousel item wrapper
			$str .= '</div></div>';
		};

		// Close swipe wrapper
		$str .= '</div>';

		if($storyCount > 1) :

			// Open carousel controls wrapper
			$str .= '<div class="carousel-controls">';

			// Add previous button
			$str .= '<div class="control prev"><div class="icon icon-arrow-left-large"></div></div>';

			// Add next button
			$str .= '<div class="control next"><div class="icon icon-arrow-right-large"></div></div>'; 

			// Close carousel controls
	    $str .= '</div>';

	  endif;

    // Open carousel pagination wrapper
    $str .= '<div class="carousel-paginate">';

    // If more than two slides, add carousel pagination
    if($storyCount > 2) :
	    for($i = 0; $i < $storyCount; $i++){
	    	if($i == 0) :
	          $dotClasses = 'active dot dot-' . $i;
	      else :
	          $dotClasses = 'dot dot-' . $i;
	      endif;
	      $str .= '<div class="' . $dotClasses . '" data-order="' . $i . '"></div>';
	    };
	  endif;

    // Close carousel pagination wrapper
    $str .= '</div>';

		// Close carousel wrapper
		$str .= '</div>';

		return $str;

	};
};

/**
*
* Display promo relationship
* Accept promo relationship object and type ('mobile' or 'widget'), return promo markup
*
**/
function display_promo($promo, $type){

  // If the promo item object is populated
  if($promo['ID']) :

  		// Get promo meta data
			$promoMeta = get_post_meta($promo['ID']);

			// Add promo meta to promo object
			$promo['description'] = $promoMeta['description'][0];
			$promo['url'] = $promoMeta['url'][0];
			$promo['cta'] = $promoMeta['cta'][0];
			$promo['new_window'] = $promoMeta['new_window'][0];

  		// Open promo wrapper, include widget class for consistent styling with sidebar widgets
  		$str = '<div class="fma-promo ' . $type . '">';

  		// Image size based on component type
  		if($type == 'mobile') :
  			$imgType = 'thumbnail';
  		else :
  			$imgType = 'location-featured';
  		endif;

  		// Get promo image as image style
  		$imgSrc = wp_get_attachment_image_src( get_post_thumbnail_id($promo['ID']), $imgType);

  		// Add promo img
  		$str .= '<img alt="' . $promo['post_title'] . '" src="' . $imgSrc[0] . '">';

  		// Open text wrapper
  		$str .= '<div class="text-wrapper">';

  		// Add promo title
  		$str .= '<h4>' . $promo['post_title'] . '</h4>';

  		// Add promo description
  		if($type == 'mobile') :
  			$str .= '<p>' . $promo['description'] . ' <span class="cta">' . $promo['cta'] . '<span class="icon icon-arrow-right"></span></span></p>';
  		else :
  			$str .= '<p>' . $promo['description'] . '</p>';
  		endif;

  		// Set button target
  		if($promo['new_window']) :
  			$btnTarget = '_blank';
  		else :
  			$btnTarget = '_self';
  		endif;

  		// Add link
  		if($type == 'mobile') :
  			$str .= '<a class="link-cover" target="' . $btnTarget . '" href="' . $promo['url'] .'"></a>';
  		else :
  			$str .= '<a class="btn" target="' . $btnTarget . '" href="' . $promo['url'] .'">' . $promo['cta'] . '</a>';
  		endif;

  		// Close text wrapper
  		$str .= '</div>';

  		// Close promo wrapper
  		$str .= '</div>';

      return $str;

  endif;
}


function process_stories($mypod){

   while( $mypod->fetch() ) {
            foreach (array('id', 'title', 'excerpt', 'content','fma_promo', 'top_image', 'is_featured', 'call_to_action', 'category', 'has_gradient') as $key => $value) {
                $item[$value] = $mypod->field($value);
                $item['fma_full'] =  get_the_post_thumbnail($item['id'], 'fma-full');
                $item['featured_top'] =  get_the_post_thumbnail($item['id'], 'featured-top');
                $item['permalink'] =  get_post_permalink($item['id']);
            }
            
              $return[]=$item;
        }


        return $return;
}



function process_menu($mypod,$daypart){
    $cachekey = 'menu-function_'.$daypart;
    $menu = pods_cache_get( $cachekey, '', function($cachekey,$mypod,$daypart) use($mypod,$daypart){
         $params = array(
                            'where' => "t.post_title = '".ucfirst($daypart)."'",
                            'orderby' => "date ASC",
                            'limit' => '1'
                        );
        $daypart_pod = pods('daypart')->find($params);

        while( $daypart_pod->fetch() ) {
            $menu_categories = $daypart_pod->field('menu_categories');
        }


        $menu = array_fill_keys(explode(', ', $menu_categories),array());
        
        if($mypod->total_found()){
            while( $mypod->fetch() ) {
                foreach (array('featured_item',
                               'title',
                               'description', 
                               'fma_promo', 
                               'story', 
                               'menu_key_relationship', 
                               'price_max', 
                               'price_tier_2',
							   'price_tier_3',
                               'price_min',
                               'daypart_relationship',
                               'menu_category', 
                               'description_1', 
                               'optional_min_price_1',
                               'optional_max_price_1',
                               'optional_price_1_tier_2', 
								'optional_price_1_tier_3',

                               'description_2', 
                               'optional_min_price_2',
                               'optional_max_price_2',
                               'optional_price_2_tier_2', 
								'optional_price_2_tier_3',

                               'description_3', 
                               'optional_min_price_3',
                               'optional_max_price_3',
                               'optional_price_3_tier_2', 
								'optional_price_3_tier_3',

                               'description_4', 
                               'optional_min_price_4',
                               'optional_max_price_4',
                               'optional_price_4_tier_2', 
								'optional_price_4_tier_3',

                               'description_5', 
                               'optional_min_price_5',
                               'optional_max_price_5',
                               'optional_price_5_tier_2', 
								'optional_price_5_tier_3'

                               ) as $key => $value) {
                     $item[$value] = $mypod->field($value);
                }

                $item['featured_img'] =  get_the_post_thumbnail( $mypod->id(), 'menu-item-featured' );
                $item['featured_img_story'] =  get_the_post_thumbnail( $mypod->id(), 'menu-item-featured-story' );
               

                switch ($daypart) {
                  case 'Dinner & Wine':
                    $daySearch = 'Dinner';

                    break;
                   case 'Lunch':
                    $daySearch = 'Lunch';

                    break;
                   case 'Breakfast':
                    $daySearch = 'Breakfast';

                    break;
                  
                  default:
                   $daySearch = 'Bakery';
                    break;
                }
                

                  if(in_array( $item['menu_category']['slug'],  explode(', ', $menu_categories) )){
                    
                    if(in_array($daySearch , $item['daypart_relationship'])){

                        $menu[$item['menu_category']['slug'] ] ['items'][] = $item;

                        if($item['featured_item'] == 1){
                            $menu[$item['menu_category']['slug']]['featured'] = $item;
                        }
                    }
                  }
            }

            pods_cache_set( $cachekey, $menu, '', $expires = 300);
            return $menu;
        }
    }); /** End pod cache */
         return $menu;
}

add_action( 'lam_process_menu', 'process_menu' );






/**
* Overwrite the lable 'Posts' with 'Stoies'
*/
add_filter(  'gettext',  'change_post_to_story'  );
add_filter(  'ngettext',  'change_post_to_story'  );
add_filter( 'esc_html', 'change_post_to_story');


function change_post_to_story( $translated ) {
  if( substr_count($translated, 'post.php')<1){
    $translated = str_ireplace(  'Post',  'Story',  $translated );  // ireplace is PHP5 only
    $translated = str_ireplace(  'Storys',  'Stories',  $translated );  // ireplace is PHP5 only
  }
  return $translated;
}




