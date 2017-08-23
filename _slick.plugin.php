<?php
/**
 * This file implements the Slick Image Carousel Widget class.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @license for Slick - MIT License (MIT) 
 * @copyright - Copyright (c) 2013-2016
 * http://kenwheeler.github.io/slick/
 * @package plugins
 *
 * Version: 1.6.0
 * Author: Ken Wheeler
 * Website: http://kenwheeler.github.io
 * Docs: http://kenwheeler.github.io/slick
 * Repo: http://github.com/kenwheeler/slick
 * Issues: http://github.com/kenwheeler/slick/issues
 * @author Adapted by Midnight Studios
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * Bootstrap Plugin
 *
 * Bootstrap Image Carousel Widget
 *
 * @package plugins
 */
class slick_plugin extends Plugin
{
	/**
	 * Variables below MUST be overriden by plugin implementations,
	 * either in the subclass declaration or in the subclass constructor.
	 */
	/**
	 * plugin name.
	 */
	var $name = 'Slick Image Carousel';
	
	/**
	 * Code, if this is a renderer or pingback plugin.
	 */
	var $code = 'slick_plg';
	var $version = '0.0.160';
	var $author = 'Jacques Joubert';
	var $help_url = 'https://github.com/midnight-studios';
	var $group = 'Midnight Studios';
	var $number_of_installs = 1;

	var $source = '';
	/**
	 * Init: This gets called after a plugin has been registered/instantiated.
	 */
	function PluginInit( & $params )
	{
		
		$this->short_desc = $this->T_('Add a Slick Image Carousel.');
		$this->long_desc = $this->T_('Include a Slick Image Carousel to your website.');

	}

	/**
	 * Get the list of dependencies that the plugin has.
	 *
	 * This gets checked on install or uninstall of a plugin.
	 *
	 * There are two <b>classes</b> of dependencies:
	 *  - 'recommends': This is just a recommendation. If it cannot get fulfilled
	 *                  there will just be a note added on install.
	 *  - 'requires': A plugin cannot be installed if the dependencies cannot get
	 *                fulfilled. Also, a plugin cannot get uninstalled, if another
	 *                plugin depends on it.
	 *
	 * Each <b>class</b> of dependency can have the following types:
	 *  - 'events_by_one': A list of eventlists that have to be provided by a single plugin,
	 *                     e.g., <code>array( array('CaptchaPayload', 'CaptchaValidated') )</code>
	 *                     to look for a plugin that provides both events.
	 *  - 'plugins':
	 *    A list of plugins, either just the plugin's classname or an array with
	 *    classname and minimum version of the plugin (see {@link Plugin::$version}).
	 *    E.g.: <code>array( 'test_plugin', '1' )</code> to require at least version "1"
	 *          of the test plugin.
	 *  - 'app_min': Minimum application (b2evo) version, e.g. "1.9".
	 *               This way you can make sure that the hooks you need are implemented
	 *               in the core.
	 *               (Available since b2evo 1.8.3. To make it work before 1.8.2 use
	 *               "api_min" and check for array(1, 2) (API version of 1.9)).
	 *  - 'api_min': You can require a specific minimum version of the Plugins API here.
	 *               If it's just a number, only the major version is checked against.
	 *               To check also for the minor version, you have to give an array:
	 *               array( major, minor ).
	 *               Obsolete since 1.9! Used API versions: 1.1 (b2evo 1.8.1) and 1.2 (b2evo 1.9).
	 *
	 * @see Plugin::GetDependencies()
	 * @return array
	 */
	function GetDependencies()
	{
		return array(
			
				'requires' => array(
					'app_min' => '6.9.3-beta',
				),
			);
	}
	
	/**
	 * 
	 */	
	function GetDefaultSettings( & $params )
	{
		global $app_version;
		
		return array(
			'plugin_sets' => array(
				'label' => T_('Carousel'),
				'note' => T_(''),
				'type' => version_compare( $app_version, '6.6.5', '>' ) ? 'array:array:string' : 'array',
			'entries' => array(),
		)
			);
	}
	
	/**
	 * Param definitions when added as a widget.
	 *
	 * Plugins used as widget need to implement the SkinTag hook.
	 *
	 * @return array
	 */
	function get_widget_param_definitions( $params )
	{
		global $app_version, $baseurl;
		
		load_funcs( 'files/model/_image.funcs.php' );
		
		return array(
		
				'section_plugin_start' => array(
					'layout' => 'begin_fieldset',
					'label'  => T_('Carousel')
				),

			'block_heading_start' => array(
					'layout' => 'begin_fieldset',
					'label'  => T_('Global')
				),
			
				// Must be named title
				'title' => array(
					'label' => T_('Title'),
					'note' => $this->T_( 'Add your Title. Also used to help identify this widget' ),
					'defaultvalue' => 'Carousel',
					'type' => 'text',
					'size' => 50,
				),
				// Must be named title
			
				'thumb_size' => array(
					'label' => T_('Image size'),
					'note' => T_('Cropping and sizing of thumbnails'),
					'type' => 'select',
					'options' => get_available_thumb_sizes(),
					'defaultvalue' => 'crop-300x100',
				),
			
			
			
				'theme_color' => array(
						'label' => T_('Theme Color'),
						'note' =>  T_('Used for arrows ect. E-g: #444444 for light-black'),
						'defaultvalue' => '#444444',
						'type' => 'color',
					),
			
		
			
								'shuffle' => array(
									'label' => T_('Shuffle Slides'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider will shuffle slides.'),
								),
	array( 'layout' => 'html', 'value' => '<!-- ==== group start ==== --> <div id="'.$this->classname.'_xs'.'" class="row"><div class="col-md-9 pull-right"><div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'. 

	/*
	*
	*	TITLE
	*
 	*/
	
	'<a data-toggle="collapse" href="#xs" style="text-decoration:none;"><div style="width:100%">Extra small devices</div></a>'.

	/*
	*
	*	
	*
 	*/   
	     
	'</h4></div><!-- panel-heading end --><div id="xs" class="panel-collapse collapse"><div class="panel-body">' ),
			
			
			
								'xs_dots' => array(
									'label' => T_('Dots'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider dots will render.'),
								),
								'xs_infinite' => array(
									'label' => T_('Infinite'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider items will repeat.'),
								),
								'xs_variableWidth' => array(
									'label' => T_('Variable Width'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('Each Image width may be different'),
								),
								'xs_autoplay' => array(
									'label' => T_('Autoplay'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider autoplay.'),
								),
								'xs_fade' => array(
									'label' => T_('Fade'),
									'note' =>  $this->T_( 'Fade on transition change. This is for displaying one slide at a time' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'xs_centerMode' => array(
									'label' => T_('Center Mode'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'xs_arrows' => array(
									'label' => T_('Arrows'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 1,
									'type' => 'checkbox',
								),
								'xs_rtl' => array(
									'label' => T_('Reverse Direction'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'xs_speed' => array(
									'label' => T_('Transition Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 500,
									'type' => 'integer',
								),
								'xs_autoplaySpeed' => array(
									'label' => T_('Autoplay Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 2000,
									'type' => 'integer',
								),
								'xs_slidesPerRow' => array(
									'label' => T_('Slide rows'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
								'xs_slidesToShow' => array(
									'label' => T_('Show Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 6,
									'type' => 'integer',
								),
								'xs_slidesToScroll' => array(
									'label' => T_('Scroll Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
								'xs_breakpoint' => array(
									'label' => T_('Device Size'),
									'note' =>  $this->T_( 'Device Size Target' ),
									'defaultvalue' => 415,
									'type' => 'integer',
								),
								'xs_slide_space' => array(
									'label' => T_('Slides Space'),
									'note' =>  $this->T_( 'Space left and right of slide in pixels. Used for spacing' ),
									'defaultvalue' => 5,
									'type' => 'integer',
								),
			
		
	array( 'layout' => 'html', 'value' => '</div></div><!-- collapse1 end --></div><!-- panel 2 end --></div><!-- panel-group end --><!-- ==== group end ==== --></div></div>' ),

/* ================================================== */
							
			
			
		
	array( 'layout' => 'html', 'value' => '<!-- ==== group start ==== --> <div id="'.$this->classname.'_sm'.'" class="row"><div class="col-md-9 pull-right"><div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'. 

	/*
	*
	*	TITLE
	*
 	*/
	
	'<a data-toggle="collapse" href="#sm" style="text-decoration:none;"><div style="width:100%">Small devices</div></a>'.

	/*
	*
	*	
	*
 	*/   
	     
	'</h4></div><!-- panel-heading end --><div id="sm" class="panel-collapse collapse"><div class="panel-body">' ),
			
			
								'sm_dots' => array(
									'label' => T_('Dots'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider dots will render.'),
								),
								'sm_infinite' => array(
									'label' => T_('Infinite'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider items will repeat.'),
								),
								'sm_variableWidth' => array(
									'label' => T_('Variable Width'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('Each Image width may be different'),
								),
								'sm_autoplay' => array(
									'label' => T_('Autoplay'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider autoplay.'),
								),
								'sm_fade' => array(
									'label' => T_('Fade'),
									'note' =>  $this->T_( 'Fade on transition change. This is for displaying one slide at a time' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'sm_centerMode' => array(
									'label' => T_('Center Mode'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'sm_arrows' => array(
									'label' => T_('Arrows'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 1,
									'type' => 'checkbox',
								),
								'sm_rtl' => array(
									'label' => T_('Reverse Direction'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'sm_speed' => array(
									'label' => T_('Transition Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 500,
									'type' => 'integer',
								),
								'sm_autoplaySpeed' => array(
									'label' => T_('Autoplay Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 2000,
									'type' => 'integer',
								),
								'sm_slidesPerRow' => array(
									'label' => T_('Slide rows'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
								'sm_slidesToShow' => array(
									'label' => T_('Show Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 6,
									'type' => 'integer',
								),
			
								'sm_slidesToScroll' => array(
									'label' => T_('Scroll Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
			
								'sm_breakpoint' => array(
									'label' => T_('Device Size'),
									'note' =>  $this->T_( 'Device Size Target' ),
									'defaultvalue' => 768,
									'type' => 'integer',
								),
								'sm_slide_space' => array(
									'label' => T_('Slides Space'),
									'note' =>  $this->T_( 'Space left and right of slide in pixels. Used for spacing' ),
									'defaultvalue' => 10,
									'type' => 'integer',
								),
		
							
		
	array( 'layout' => 'html', 'value' => '</div></div><!-- collapse1 end --></div><!-- panel 2 end --></div><!-- panel-group end --><!-- ==== group end ==== --></div></div>' ),

/* ================================================== */
	
			
	
		
	array( 'layout' => 'html', 'value' => '<!-- ==== group start ==== --> <div id="'.$this->classname.'_md'.'" class="row"><div class="col-md-9 pull-right"><div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'. 

	/*
	*
	*	TITLE
	*
 	*/
	
	'<a data-toggle="collapse" href="#md" style="text-decoration:none;"><div style="width:100%">Medium devices</div></a>'.

	/*
	*
	*	
	*
 	*/   
	     
	'</h4></div><!-- panel-heading end --><div id="md" class="panel-collapse collapse"><div class="panel-body">' ),
			
								'md_dots' => array(
									'label' => T_('Dots'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider dots will render.'),
								),
								'md_infinite' => array(
									'label' => T_('Infinite'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider items will repeat.'),
								),
								'md_variableWidth' => array(
									'label' => T_('Variable Width'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('Each Image width may be different'),
								),
								'md_autoplay' => array(
									'label' => T_('Autoplay'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider autoplay.'),
								),
								'md_fade' => array(
									'label' => T_('Fade'),
									'note' =>  $this->T_( 'Fade on transition change. This is for displaying one slide at a time' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'md_centerMode' => array(
									'label' => T_('Center Mode'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'md_arrows' => array(
									'label' => T_('Arrows'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 1,
									'type' => 'checkbox',
								),
								'md_rtl' => array(
									'label' => T_('Reverse Direction'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'md_speed' => array(
									'label' => T_('Transition Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 500,
									'type' => 'integer',
								),
								'md_autoplaySpeed' => array(
									'label' => T_('Autoplay Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 2000,
									'type' => 'integer',
								),
								'md_slidesPerRow' => array(
									'label' => T_('Slide rows'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
								'md_slidesToShow' => array(
									'label' => T_('Show Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 6,
									'type' => 'integer',
								),
								'md_slidesToScroll' => array(
									'label' => T_('Scroll Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
								'md_breakpoint' => array(
									'label' => T_('Device Size >'),
									'note' =>  $this->T_( 'Device Size Target' ),
									'defaultvalue' => 992,
									'type' => 'integer',
								),
								'md_slide_space' => array(
									'label' => T_('Slides Space'),
									'note' =>  $this->T_( 'Space left and right of slide in pixels. Used for spacing' ),
									'defaultvalue' => 15,
									'type' => 'integer',
								),
		
	array( 'layout' => 'html', 'value' => '</div></div><!-- collapse1 end --></div><!-- panel 2 end --></div><!-- panel-group end --><!-- ==== group end ==== --></div></div>' ),

/* ================================================== */
	
			
	array( 'layout' => 'html', 'value' => '<!-- ==== group start ==== --> <div id="'.$this->classname.'_lg'.'" class="row"><div class="col-md-9 pull-right"><div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'. 

	/*
	*
	*	TITLE
	*
 	*/
	
	'<a data-toggle="collapse" href="#lg" style="text-decoration:none;"><div style="width:100%">Large devices</div></a>'.

	/*
	*
	*	
	*
 	*/   
	     
	'</h4></div><!-- panel-heading end --><div id="lg" class="panel-collapse collapse"><div class="panel-body">' ),
			
			
								'lg_dots' => array(
									'label' => T_('Dots'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider dots will render.'),
								),
								'lg_infinite' => array(
									'label' => T_('Infinite'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider items will repeat.'),
								),
								'lg_variableWidth' => array(
									'label' => T_('Variable Width'),
									'defaultvalue' => 0,
									'type' => 'checkbox',
									'note' =>  T_('Each Image width may be different'),
								),
								'lg_autoplay' => array(
									'label' => T_('Autoplay'),
									'defaultvalue' => 1,
									'type' => 'checkbox',
									'note' =>  T_('If enabled the slider autoplay.'),
								),
								'lg_fade' => array(
									'label' => T_('Fade'),
									'note' =>  $this->T_( 'Fade on transition change. This is for displaying one slide at a time' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'lg_centerMode' => array(
									'label' => T_('Center Mode'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'lg_arrows' => array(
									'label' => T_('Arrows'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 1,
									'type' => 'checkbox',
								),
								'lg_rtl' => array(
									'label' => T_('Reverse Direction'),
									'note' =>  $this->T_( 'Focus on center Iitem' ),
									'defaultvalue' => 0,
									'type' => 'checkbox',
								),
								'lg_speed' => array(
									'label' => T_('Transition Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 500,
									'type' => 'integer',
								),
								'lg_autoplaySpeed' => array(
									'label' => T_('Autoplay Speed'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 2000,
									'type' => 'integer',
								),
								'lg_slidesPerRow' => array(
									'label' => T_('Slide rows'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
			
								'lg_slidesToShow' => array(
									'label' => T_('Show Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 6,
									'type' => 'integer',
								),
			
								'lg_slidesToScroll' => array(
									'label' => T_('Scroll Slides'),
									'note' =>  $this->T_( '' ),
									'defaultvalue' => 1,
									'type' => 'integer',
								),
			
								'lg_breakpoint' => array(
									'label' => T_('Device Size >'),
									'note' =>  $this->T_( 'Device Size Target' ),
									'defaultvalue' => 1200,
									'type' => 'integer',
								),
								'lg_slide_space' => array(
									'label' => T_('Slides Space'),
									'note' =>  $this->T_( 'Space left and right of slide in pixels. Used for spacing' ),
									'defaultvalue' => 20,
									'type' => 'integer',
								),
		
	array( 'layout' => 'html', 'value' => '</div></div><!-- collapse1 end --></div><!-- panel 2 end --></div><!-- panel-group end --><!-- ==== group end ==== --></div></div>' ),

/* ================================================== */
			
			
			'block_heading_end' => array(
					'layout' => 'end_fieldset',
				),
			
			
			'block_item_start' => array(
					'layout' => 'begin_fieldset',
					'label'  => T_('Items')
				),
			
			'item_headingfont' => array(
						'label' => T_('Tile'),
						'type'  => 'input_group',
						'inputs' => array(
							'_size' => array(
								'label' => T_('Size'),
									'defaultvalue' => '18px',
									'options'      => array(
										'8px'        => T_('Very Small (8px)'),
										'10px'        => T_('Small (10px)'),
										'12px'        => T_('Default (12px)'),
										'14px'       => T_('Standard (14px)'),
										'14px'       => T_('Standard (14px)'),
										'16px'         => T_('Medium (16px)'),
										'18px'          => T_('Large (18px)'),
										'20px'          => T_('Larger (20px)'),
										'22px'          => T_('Very Large (22px)'),
										'24px'     => T_('Extra large (24px)'),
										'40px'     => T_('Gigantic (40px)'),
									),
								'type' => 'select'
							),
							'_weight' => array(
								'label' => T_('Weight'),
								'defaultvalue' => '400',
								'options' => array(
										'100' => '100',
										'200' => '200',
										'300' => '300',
										'400' => '400 ('.T_('Normal').')',
										'500' => '500',
										'600' => '600',
										'700' => '700 ('.T_('Bold').')',
										'800' => '800',
										'900' => '900',
									),
								'type' => 'select',
							),
								'_style' => array(
									'label' => T_('Style'),
									'defaultvalue' => 'oblique',
									'options' => array(
											'oblique' => 'Oblique',
											'italic' => 'Italic',
											'normal' => 'Normal',
										),
									'type' => 'select',
								),
							'_color' => array(
									'label' => T_('Color'),
									'note' =>  T_('E-g: #444444 for light-black'),
									'defaultvalue' => '#444444',
									'type' => 'color',
								),
							'_transform' => array(
												'label' => T_('Transform'),
												'note' =>  T_('Transform to uppercase?'),
												'defaultvalue' => 'uppercase',
												'options' => array(
														'uppercase'		=> T_('UPPERCASE'),
														'lowercase'		=> T_('lowercase'),
														'capitalize'	=> T_('Capitalize'),
														'none'          => T_('none'),
													),
												'type' => 'select',
												),
						)
					),
			
			'item_subheadingfont' => array(
						'label' => T_('Subtitle'),
						'type'  => 'input_group',
						'inputs' => array(
							'_size' => array(
								'label' => T_('Size'),
									'defaultvalue' => '16px',
									'options'      => array(
										'8px'        => T_('Very Small (8px)'),
										'10px'        => T_('Small (10px)'),
										'12px'        => T_('Default (12px)'),
										'14px'       => T_('Standard (14px)'),
										'14px'       => T_('Standard (14px)'),
										'16px'         => T_('Medium (16px)'),
										'18px'          => T_('Large (18px)'),
										'20px'          => T_('Larger (20px)'),
										'22px'          => T_('Very Large (22px)'),
										'24px'     => T_('Extra large (24px)'),
										'40px'     => T_('Gigantic (40px)'),
									),
								'type' => 'select'
							),
							'_weight' => array(
								'label' => T_('Weight'),
								'defaultvalue' => '400',
								'options' => array(
										'100' => '100',
										'200' => '200',
										'300' => '300',
										'400' => '400 ('.T_('Normal').')',
										'500' => '500',
										'600' => '600',
										'700' => '700 ('.T_('Bold').')',
										'800' => '800',
										'900' => '900',
									),
								'type' => 'select',
							),
								'_style' => array(
									'label' => T_('Style'),
									'defaultvalue' => 'oblique',
									'options' => array(
											'oblique' => 'Oblique',
											'italic' => 'Italic',
											'normal' => 'Normal',
										),
									'type' => 'select',
								),
							'_color' => array(
									'label' => T_('Color'),
									'note' =>  T_('E-g: #444444 for light-black'),
									'defaultvalue' => '#444444',
									'type' => 'color',
								),
							'_transform' => array(
												'label' => T_('Transform'),
												'note' =>  T_('Transform to uppercase?'),
												'defaultvalue' => 'uppercase',
												'options' => array(
														'uppercase'		=> T_('UPPERCASE'),
														'lowercase'		=> T_('lowercase'),
														'capitalize'	=> T_('Capitalize'),
														'none'          => T_('none'),
													),
												'type' => 'select',
												),
						)
					),
			

			'block_item_end' => array(
					'layout' => 'end_fieldset',
				),

			
			
				'block_no_start' => array(
					'layout' => 'begin_fieldset',
					'label'  => T_('No Image Font')
				),
							
						'no_imagefont' => array(
							'label' => T_('Body'),
							'type'  => 'input_group',
							'inputs' => array(
								'_size' => array(
									'label' => T_('Size'),
									'defaultvalue' => '14px',
									'options'      => array(
										'8px'        => T_('Very Small (8px)'),
										'10px'        => T_('Small (10px)'),
										'12px'        => T_('Default (12px)'),
										'14px'       => T_('Standard (14px)'),
										'14px'       => T_('Standard (14px)'),
										'16px'         => T_('Medium (16px)'),
										'18px'          => T_('Large (18px)'),
										'20px'          => T_('Larger (20px)'),
										'22px'          => T_('Very Large (22px)'),
										'24px'     => T_('Extra large (24px)'),
										'40px'     => T_('Gigantic (40px)'),
									),
									'type' => 'select'
								),
								'_weight' => array(
									'label' => T_('Weight'),
									'defaultvalue' => '400',
									'options' => array(
											'100' => '100',
											'200' => '200',
											'300' => '300',
											'400' => '400 ('.T_('Normal').')',
											'500' => '500',
											'600' => '600',
											'700' => '700 ('.T_('Bold').')',
											'800' => '800',
											'900' => '900',
										),
									'type' => 'select',
								),
								'_style' => array(
									'label' => T_('Style'),
									'defaultvalue' => 'oblique',
									'options' => array(
											'oblique' => 'Oblique',
											'italic' => 'Italic',
											'normal' => 'Normal',
										),
									'type' => 'select',
								),
								'_color' => array(
										'label' => T_('Color'),
										'note' =>  T_('E-g: #444444 for light-black'),
										'defaultvalue' => '#444444',
										'type' => 'color',
									),
								'_transform' => array(
													'label' => T_('Transform'),
													'note' =>  T_('Transform to uppercase?'),
													'defaultvalue' => 'uppercase',
													'options' => array(
															'uppercase'		=> T_('UPPERCASE'),
															'lowercase'		=> T_('lowercase'),
															'capitalize'	=> T_('Capitalize'),
															'none'          => T_('none'),
														),
													'type' => 'select',
													),
							)
						),
								
		'block_no_end' => array(
					'layout' => 'end_fieldset',
				),

					'force' => array(
						'label' => T_('Force Template'),
						'defaultvalue' => 1,
						'type' => 'checkbox',
						'note' => T_('If enabled the plugin will overide Skin template params.'),
					),
			
			'plugin_sets' => array(
			
			
				'label' => T_('Slide Item'),
				'note' => T_('Click to add another set'),
				'type' => version_compare( $app_version, '6.6.5', '>' ) ? 'array:array:string' : 'array',
				'max_count' => 10,
				'entries' => array(
			
					'item_disabled' => array(
						'label' => T_('Disabled'),
						'defaultvalue' => 0,
						'type' => 'checkbox',
						'note' => T_('It will be ignored if disabled'),
					),
			
				'file_ID' => array(
					'label' => T_('Image'),
					'defaultvalue' => '',
					'type' => 'fileselect',
					'thumbnail_size' => 'fit-320x320',
				),
			/*
				'image_source' => array(
					'label' => T_('Fallback image source'),
					'note' => '',
					'type' => 'radio',
				
					'options' => array(
							array( 'none', T_('Disabled') ),
							array( 'skin', T_('Skin folder') ),
							array( 'coll', T_('Collection File Root') ),
							array( 'shared', T_('Shared File Root') ),
							array( 'plugin', T_('Plugin File Root') ),
							array( 'external', T_('External File') ) ),
					'defaultvalue' => 'none',
			
					'class' => $this->classname.'_image_source',
				),
			
				'image_fallback_url' => array(
					'label' => T_('Fallback image path'),
					'note' => '<span id="'.$this->classname.'_image_note">'.$this->T_( 'If no file was selected. Relative to the root of the selected source.' ).'</span>',
					'defaultvalue' => '',
					'valid_pattern' => array( 'pattern'=>'~'.$local.'~i',
											  'error'=>T_('Invalid filename.') ),
					// the following is necessary to catch user input value of "<". Otherwise, "<" and succeeding characters
					// will translate to an empty string and pass the regex pattern below
					'type' => 'html_textarea',
					'allow_empty' => true,
					'id' => $this->classname.'_image_path',
					//'onchange' => 'validateUrl(this);',
					//'onkeyup' => 'validateUrl(this);',
					//'onkeydown' => 'validateUrl(this);',
					'rows' => 1,
				),
			*/
			
				'heading' => array(
					'label' => T_('Heading'),
					'note' => $this->T_( 'Give your entry a heading.' ),
					'defaultvalue' => 'Heading',
					'type' => 'html_textarea',
					'rows' => 1,
				),
			
				'subheading' => array(
					'label' => T_('Sub Heading'),
					'note' => $this->T_( 'Give your entry a sub heading.' ),
					'defaultvalue' => 'Sub Heading',
					'type' => 'html_textarea',
					'rows' => 1,
				),
			
			
				'body' => array(
					'label' => T_('No Image'),
					'note' => $this->T_( 'No Image content.' ),
					'defaultvalue' => 'No Image',
					'type' => 'html_textarea',
					'rows' => 3,
				),
			
			/*
			'href' => array(
							'label' => T_('Link'),
							'type'  => 'input_group',
							'inputs' => array(
											'_src' => array(
												'label' => T_('href'),
												'defaultvalue' => $baseurl,
												'type' => 'text',
												'size' => 50,
											),
											'_title' => array(
												'label' => T_('Title'),
												'defaultvalue' => 'Logo',
												'type' => 'text',
												'size' => 50,
											),
											'_class' => array(
												'label' => T_('Class'),
												'defaultvalue' => 'slide_link',
												'type' => 'text',
											),
											'_target' => array(
												'label' => T_('Target'),
												'defaultvalue' => '_blank',
												'type' => 'select',
												'options' => array(
																'_blank' => T_('New'),
																'_self' => T_('Self'),
																'_parent' => T_('Parent'),
																'_top' => T_('Top'))),
											))
											
			*/								
			
			
											'href_src' => array(
												'label' => T_('href'),
												'defaultvalue' => $baseurl,
												'type' => 'text',
												'size' => 50,
											),
											'href_title' => array(
												'label' => T_('Title'),
												'defaultvalue' => 'Logo',
												'type' => 'text',
												'size' => 50,
											),
											'href_class' => array(
												'label' => T_('Class'),
												'defaultvalue' => 'slide_link',
												'type' => 'text',
											),
											'href_target' => array(
												'label' => T_('Target'),
												'defaultvalue' => '_blank',
												'type' => 'select',
												'options' => array(
																'_blank' => T_('New'),
																'_self' => T_('Self'),
																'_parent' => T_('Parent'),
																'_top' => T_('Top'))),
											
			
			
			),
	
			),
					'section_plugin_end' => array(
					'layout' => 'end_fieldset',
				),
		
		);
	}
	
	/**
	 * 	accepted params:
	 *		
	 *		file_ID			:   numeric / integer - gets assigned when selecting a file. This is for files in FileCache. If blank it will look at the fallback path
	 *		thumb_size		:
	 *		image_fallback_url		:
	 *		image_source	:
	 *		
	 */	
	function get_image_attrib( $params, $blog = 1 )
	{
		
		global $Blog, $skins_url, $thumbnail_sizes;

		
		if( empty( $Blog ) )
		{
			
			// Getting current blog info:
			$BlogCache = & get_BlogCache();
			/**
			* @var Blog
			*/
			$Blog = & $BlogCache->get_by_ID( $blog, false, false );

			if( empty( $Blog ) )
			{
				return;
				// EXIT.
			}

		}

		$img_attribs = array( 'src' => '' );
		
		if( ! empty( $params['file_ID'] ) )	
		{
			$FileCache = & get_FileCache();
			
			$File = & $FileCache->get_by_ID( $params['file_ID'], false );
		
			if( ! empty( $File ) )
			{
					$this->source = 'FileCache';

					if( file_exists( $File->get_full_path() ) )
					{
						
						if( $params['thumb_size'] == 'original' )
						{	// We want src to link to the original file
							$img_attribs['src'] = $File->get_url();
							$img_attribs['alt'] = strval( $File->get( 'alt' ) );

						}
						else {
							
							$img_attribs = $File->get_img_attribs($params['thumb_size']);
							
						}
						
						if( $File->check_image_sizes( $params['thumb_size'], 64, $img_attribs ) )
						{ // If image larger than 64x64 add class to display animated gif during loading
							$img_attribs['class'] = ' loadimg';
						}
						
					}
			} 
		
		}
		
		/*
		*	$File is defined:
		*			a url is defined in $img_attribs['src']
		*
		*	$File is empty:
		*			then we need to look if a fallback image is specified
		*	
		*			look @ $params['image_fallback_url'] can be relative or absolute depending on the source
		*			look @ $params['image_source'] none | skin | shared | plugin | external | coll
		*/
		if( empty( $img_attribs['src'] ) )
		{
			// for reference
			$this->source = 'Fallback';
			
			// no fallback image and no selected file
			if( empty( $params['image_fallback_url'] ) && empty( $File ) ) return '';
			
			switch( $params['image_source'] )
			{	
				// no fallback image 	
				case 'none':
					return '';
					
				case 'skin':
					global $skins_path;
					$img_attribs['src'] = $skins_url.$Blog->get_skin_folder().'/'.$params['image_fallback_url'];
					$img_attribs['path'] = $skins_path.$Blog->get_skin_folder().'/'.$params['image_fallback_url'];
					break;

				case 'shared':
					global $media_url, $media_path;
					$img_attribs['src'] = $media_url.'shared/'.$params['image_fallback_url'];
					$img_attribs['path'] = $media_path.'shared/'.$params['image_fallback_url'];
					break;

				case 'plugin':
					global $media_url, $media_path;
					$img_attribs['src'] = $this->get_plugin_url().$params['image_fallback_url'];
					$img_attribs['path'] = dirname(__FILE__).'/'.$params['image_fallback_url'];
					break;
					
				case 'external': 
					// from remote source 
					if( is_absolute_url( $params['image_fallback_url'] ) )
					{	// check if remote file exists
						if( ! $this->remote_file_exists( $params['image_fallback_url'] ) ) return '';
						// okay, use fallback image url
						$img_attribs['src'] = $params['image_fallback_url'];
						
					}
					else
					{	
						//the source is set for remote source but the url is not absolute
						return '';
					}
					break;

				case 'coll':// Collection
				default:
					$img_attribs['src'] = $Blog->get_media_url().$params['image_fallback_url'];
					$img_attribs['path'] = $Blog->get_media_dir().$params['image_fallback_url'];	
						
					break;
			}
			
			// check if the url points to an existing file
			if( ! is_absolute_url( $img_attribs['src'] ) )
			{

				if( empty( $img_attribs['src'] ) || ! file_exists( $img_attribs['src'] ) || ! is_file( $img_attribs['src'] ) )
				{
					// relative url that is invalid. exit
					return '';
				}

			}
			
			else {
				
				// check if absolute url points to an existing file
				if( ! $this->remote_file_exists( $img_attribs['src'] ) ) return '';
			}
		
		}
		
		
		// the image does not have 'alt'
		if( empty( $img_attribs['alt'] ) ) 
		{ 
			/*
			*	check if params contains the key for 'alt'
			*/
			if ( array_key_exists('alt', $params ) )
			{
				/*
				*	check if params contains the key for 'title'
				*/
				if ( array_key_exists('title', $params ) )
				{
					/*
					*	key for 'alt' found in params, is it defined?
					*/
					if( empty( $params['alt'] ) )
					{
						// 'alt' is empty, use value from title
						$params['alt'] = $params['title'];
					}

				}
				
				// assign alt to image attributes
				$img_attribs['alt'] = $params['alt']; 
			}
			
		}
		
		// check if style is defined in params
		if ( array_key_exists('style', $params ) )
		{
			if( ! empty( $params['style'] ) )
			{
				// assign style to image attributes
				$img_attribs['style'] = format_to_output( $params['style'], 'htmlattr' );
			}
		}
		
		// check if class is defined in params
		if ( array_key_exists('class', $params ) )
		{
			if( ! empty( $params['class'] ) )
			{
				// assign class to image attributes
				$img_attribs['class'] = $img_attribs['class'].' '.$params['class'];
			}
		}
		
		// return image attributes
		return $img_attribs;
		
	}
	
	/**
	 * Event handler: Called at the end of the skin's HTML HEAD section.
	 *
	 * Use this to add any HTML HEAD lines (like CSS styles or links to resource files (CSS, JavaScript, ..)).
	 *
	 * @param array Associative array of parameters
	 */
	function SkinEndHtmlHead( & $params )
	{
		
		$plug_url = $this->get_plugin_url();
	
		echo '<link type="text/css" href="'.$plug_url.'assets/front-office/css/slick.css" rel="stylesheet"/>'."\n\n";
		echo '<link type="text/css" href="'.$plug_url.'assets/front-office/css/slick-theme.css" rel="stylesheet"/>'."\n\n";
		echo '<script type="text/javascript" src="'.$plug_url.'assets/front-office/js/slick.js"></script>'."\n\n";
	}

	/**
	 * Event handler: Called at the beginning  of the "Edit wdiget" form on back-office.
	 *
	 * @param array Associative array of parameters
	 *   - 'Form': the {@link Form} object (by reference)
	 *   - 'ComponentWidget': the Widget which gets edited (by reference)
	 * @return boolean did we display something?
	 */
	function WidgetBeginSettingsForm( & $params )
	{
		global $wi_ID;
		
		$plug_url = $this->get_plugin_url();
		
		/*
		* Limit the items to ONLY THIS PLUGIN we identify by the plugin CODE
		* Limit the items to only when we the WIDGETS FORM is loaded {@see inc/widgets/views/_widget.form.php}
		*/
		
		if( !empty( $wi_ID ) )
		{
			// Load Enabled Widget Cache
			$EnabledWidgetCache = & get_EnabledWidgetCache();
			
			// Load Widget by THIS ID
			$Widget = & $EnabledWidgetCache->get_by_ID( $wi_ID );
			
			/* 
			* This is in the 'header' of the widgets form.
			* This StyleSheet adds style classess required by JavaScript for user notices;
			* @see /assets/
			*/	
			if( $Widget->code != $this->code  )  return true;
			// Add code here that must be rendered for this plugin only
		}

		return true;
	}

	/**
	 * We wish to add some css or js in the header
	 *
	 * Event handler: Called when ending the admin html head section.
	 *
	 * @param array Associative array of parameters
	 * @return boolean did we do something?
	 */
	function AdminEndHtmlHead( & $params )
	{
	  	global $plugin_ID, $Blog, $wi_ID;
		
		$plug_url = $this->get_plugin_url();
		
		/**
		*	We only want to load files for THIS widget
		*/ 		
		if( !empty( $wi_ID ) )
		{
			// Load Enabled Widget Cache
			$EnabledWidgetCache = & get_EnabledWidgetCache();
			
			// Load Widget by THIS ID
			$Widget = & $EnabledWidgetCache->get_by_ID( $wi_ID );
			
			if( $Widget->code != $this->code  )  return true;
			
			/*
			* Limit the items to ONLY THIS PLUGIN we identify by the plugin CODE
			* Limit the items to only when we the WIDGETS page is displayed
			*/
			
			
			if( ( param( 'ctrl', 'string' ) == 'widgets' && param( 'blog', 'integer', true ) == $Blog->ID && $Widget->code == $this->code ) || ( param( 'ctrl', 'string' ) == 'coll_settings' &&  param( 'tab', 'string' ) == 'plugins' && param( 'blog', 'integer', true ) == $Blog->ID  &&  param( 'plugin_group', 'string' ) == $this->group ))
			{
				// Add code here that must be rendered for this plugin only
				// Plugin Collection Settings
			}
		}
			
			/*
			* Limit the items to ONLY THIS PLUGIN we identify by the plugin ID
			* Limit the items to only when the PLUGIN page is displayed
			*/
		if(  ( param( 'ctrl', 'string' ) == 'plugins' &&  param( 'action', 'string' ) == 'edit_settings' && param( 'plugin_ID', 'integer', true ) == $plugin_ID ) ) 
		 {
				// Add code here that must be rendered for this plugin only
			 	// Plugin Global Settings
			  
		  }

		return true;
	}
	
	/**
	 * 	Create css
	 * 	returns string
	 * 	
	 */	
	function build_css( $pre_fix, $font_array, $params, $target_element, $arr_name = 'css' )
	{
		
		if( is_array( $font_array ) )
		{
			foreach( $font_array as $idx => $attr )
			{

				if( $value = $params[ $pre_fix.$attr ] )
				{ 
					switch($idx)
					{
						case 0:
						case 1:
						case 2:
							${$pre_fix.$arr_name}[] = "\t".'font-'.$attr.': '.$value.' !important;'."\n";
							break;
						case 3:
							${$pre_fix.$arr_name}[] = "\t".$attr.': '.$value.' !important;'."\n";
							break;
						case 4:
							${$pre_fix.$arr_name}[] = "\t".'text-'.$attr.': '.$value.' !important;'."\n";
							break;
						default:

							break;

					}

				}

			}
		}
		
			// Prepare the complete CSS for font customization
			if( ! empty( ${$pre_fix.'css'} ) )
			{
				$custom_css = " $target_element { \n".implode( ' ', ${$pre_fix.'css'} )." }\n";
			}
			else
			{
				$custom_css = '';
			}	
		
		return $custom_css;
		
	}
	
	/**
	 * Display the widget!
	 *
	 * @param array MUST contain at least the basic display params
	 */
	function SkinTag( & $params )
	{
		global $wi_ID;
			
		$wi_ID = $params['wi_ID'];
		
		$plugin_sets = $params['plugin_sets'];
		
		if( empty( $plugin_sets ) )
		{
			return false; 
		}
		
		global $thumbnail_sizes;
		
		$key = 'item_disabled';
		$value = 0;
		
		/*
		*	The idea is to set the div element width for slides that does not contain images on the width based on the thumbnail
		*	the challenge is responsive changes for device widths and slides on display.
		*	The image size really only take effect when there are less slides being displayed.
		*
		
		*/
		if( isset( $thumbnail_sizes[ $params['thumb_size'] ] ) )
		{ 

			$width = $thumbnail_sizes[ $params['thumb_size'] ][1];
			$height = $thumbnail_sizes[ $params['thumb_size'] ][2] ;
			$ratio = $width / $height;
		}	
		
		
		/*
		*	use this to count how many params with specific key matches a specific value;
		*	we use this for example to check how many slides are disabled / enabled
		*	useful for Inline JavaScript
		*	@returns integer
		*/
		
		#$key = 'item_disabled';
		#$value = 1;
		//$c = count( array_filter( $plugin_sets, function( $element ) use( $key, $value ){ return $element[$key] == $value; }));
		
		$custom_css = '';
		
		$custom_css .= "\n\n".'<style type="text/css">'."\n" ;
		
		/*
		*	Automate repetative tasks
		*/
		$font_array = array('size', 'weight', 'style', 'color', 'transform');

		$pre_fix = 'item_headingfont_'; //Size, Weight, Style, Color, Transform
		$target_element = '#slick_'.$this->classname.'_'.$wi_ID.' .heading';
		$custom_css .= $this->build_css( $pre_fix, $font_array, $params, $target_element );
		
		$pre_fix = 'item_subheadingfont_'; //Size, Weight, Style, Color, Transform
		$target_element = '#slick_'.$this->classname.'_'.$wi_ID.' .subheading';
		$custom_css .= $this->build_css( $pre_fix, $font_array, $params, $target_element );
		
		$pre_fix = 'no_imagefont_'; //Size, Weight, Style, Color, Transform
		$target_element = '#slick_'.$this->classname.'_'.$wi_ID.' .item-text-only ';
		$custom_css .= $this->build_css( $pre_fix, $font_array, $params, $target_element );
		
		$custom_css .= '.center-item {'."\n";
		$custom_css .= "\t".'position: relative;'."\n";
		$custom_css .= "\t".'top:0;'."\n";
		$custom_css .= "\t".'left:50%;'."\n";
		$custom_css .= "\t".'-ms-transform: translate(-50%);'."\n";
		$custom_css .= "\t".'-webkit-transform: translate(-50%);'."\n";
		$custom_css .= "\t".'transform: translate(-50%);'."\n";
		$custom_css .= '}'."\n\n";

		$custom_css .= '#slider-text {'."\n";
		$custom_css .= "\t".'padding-top: 40px;'."\n";
		$custom_css .= "\t".'display: block;'."\n";
		$custom_css .= '}'."\n\n";
		
		// Smart Phones
		$custom_css .= '@media (max-width:768px) {'."\n";
		if( $value = $params[ 'xs_slide_space' ] )
		{
			$custom_css .= "\t".'#slick_'.$this->classname.'_'.$wi_ID.' .slick-slide {'."\n";
			$custom_css .= "\t\t".'margin: 0px '.$value.'px;'."\n";
			$custom_css .= "\t".'}'."\n";
		
		}
		$custom_css .= '}'."\n\n";
		
		// Most Tablets
		$custom_css .= '@media (min-width:768px) {'."\n";
		if( $value = $params[ 'sm_slide_space' ] )
		{
			$custom_css .= "\t".'#slick_'.$this->classname.'_'.$wi_ID.' .slick-slide {'."\n";
			$custom_css .= "\t\t".'margin: 0px '.$value.'px;'."\n";
			$custom_css .= "\t".'}'."\n";
		
		}
		$custom_css .= '}'."\n\n";
		
		// Most Laptops / small desktops
		$custom_css .= '@media (min-width:992px) {'."\n";
		if( $value = $params[ 'md_slide_space' ] )
		{
			$custom_css .= "\t".'#slick_'.$this->classname.'_'.$wi_ID.' .slick-slide {'."\n";
			$custom_css .= "\t\t".'margin: 0px '.$value.'px;'."\n";
			$custom_css .= "\t".'}'."\n";
		
		}
		$custom_css .= '}'."\n\n";
		
		// Large Desktops
		$custom_css .= '@media (min-width:1200px) {'."\n";
		if( $value = $params[ 'lg_slide_space' ] )
		{
			$custom_css .= "\t".'#slick_'.$this->classname.'_'.$wi_ID.' .slick-slide {'."\n";
			$custom_css .= "\t\t".'margin: 0px '.$value.'px;'."\n";
			$custom_css .= "\t".'}'."\n";
		
		}
		$custom_css .= '}'."\n\n";
		
		// Target the arrows
		if( $value = $params[ 'theme_color' ] )
		{
			$custom_css .= '#slick_'.$this->classname.'_'.$wi_ID.' .slick-prev:before,'."\n".'#slick_'.$this->classname.'_'.$wi_ID.' .slick-next:before {'."\n";
			$custom_css .= "\t".'color: '.$value.';'."\n";
			$custom_css .= '}'."\n";
		
		}

		$custom_css .= '</style>'."\n\n";
			        	
		$custom_js = '';
		$custom_js .= '<script type="text/javascript">'."\n\n";
		$custom_js .= '$(document).ready(function(){'."\n";
		$custom_js .= '"use strict";'."\n";
		
		
		if( $value = $params[ 'shuffle' ] )
		{
		
			// shuffle the slides and then call slick
			$custom_js .= 'console.log("shuffle");';
			$custom_js .= '$("#slick_'.$this->classname.'_'.$wi_ID.' div").shuffle();'."\n";
		
		}
		
		$custom_js .= '$("#slick_'.$this->classname.'_'.$wi_ID.'").slick({'."\n";
		
		$custom_js .= ( $params['md_dots'] == 0 ) ? 'dots: false,'."\n":'dots: true,'."\n";
		$custom_js .= ( $params['md_infinite'] == 0 ) ? 'infinite: false,'."\n":'infinite: true,'."\n";
		$custom_js .= ( $params['md_variableWidth'] == 0 ) ? 'variableWidth: false,'."\n":'variableWidth: true,'."\n";
		$custom_js .= ( $params['md_autoplay'] == 0 ) ? 'autoplay: false,'."\n":'autoplay: true,'."\n";
		$custom_js .= ( $params['md_fade'] == 0 ) ? 'fade: false,'."\n":'fade: true,'."\n";
		$custom_js .= ( $params['md_centerMode'] == 0 ) ? 'centerMode: false,'."\n":'centerMode: true,'."\n";
		$custom_js .= ( $params['md_arrows'] == 0 ) ? 'arrows: false,'."\n":'arrows: true,'."\n";
		$custom_js .= ( $params['md_rtl'] == 0 ) ? 'rtl: false,'."\n":'rtl: true,'."\n";
		
		$custom_js .= 'speed:'.$params['md_speed'].','."\n";
		
		$custom_js .= 'centerPadding: \'40px\','."\n";
		
		$custom_js .= 'autoplaySpeed:'.$params['md_autoplaySpeed'].','."\n";
		$custom_js .= 'slidesPerRow:'.$params['md_slidesPerRow'].','."\n";
		$custom_js .= 'slidesToShow:'.$params['md_slidesToShow'].','."\n";
		$custom_js .= 'slidesToScroll:'.$params['md_slidesToScroll'].','."\n";

		
		$custom_js .= 'responsive: ['."\n";
		$custom_js .= '{'."\n";
		$custom_js .= 'breakpoint:'.$params['xs_breakpoint'].','."\n";
		$custom_js .= "\t".'settings: {'."\n";
		$custom_js .= ( $params['xs_dots'] == 0 ) ? 'dots: false,'."\n":'dots: true,'."\n";
		$custom_js .= ( $params['xs_infinite'] == 0 ) ? 'infinite: false,'."\n":'infinite: true,'."\n";
		$custom_js .= ( $params['xs_variableWidth'] == 0 ) ? 'variableWidth: false,'."\n":'variableWidth: true,'."\n";
		$custom_js .= ( $params['xs_autoplay'] == 0 ) ? 'autoplay: false,'."\n":'autoplay: true,'."\n";
		$custom_js .= ( $params['xs_fade'] == 0 ) ? 'fade: false,'."\n":'fade: true,'."\n";
		$custom_js .= ( $params['xs_centerMode'] == 0 ) ? 'centerMode: false,'."\n":'centerMode: true,'."\n";
		$custom_js .= ( $params['xs_arrows'] == 0 ) ? 'arrows: false,'."\n":'arrows: true,'."\n";
		$custom_js .= ( $params['xs_rtl'] == 0 ) ? 'rtl: false,'."\n":'rtl: true,'."\n";
		
		$custom_js .= 'speed:'.$params['xs_speed'].','."\n";
		$custom_js .= 'centerPadding: \'40px\','."\n";
		$custom_js .= 'autoplaySpeed:'.$params['xs_autoplaySpeed'].','."\n";
		$custom_js .= 'slidesPerRow:'.$params['xs_slidesPerRow'].','."\n";
		$custom_js .= 'slidesToShow:'.$params['xs_slidesToShow'].','."\n";
		$custom_js .= 'slidesToScroll:'.$params['xs_slidesToScroll'].','."\n";
		$custom_js .= "\t".'}'."\n";
		$custom_js .= '},'."\n";
		$custom_js .= '{'."\n";
		$custom_js .= 'breakpoint:'.$params['sm_breakpoint'].','."\n";
		$custom_js .= "\t".'settings: {'."\n";
		$custom_js .= ( $params['sm_dots'] == 0 ) ? 'dots: false,'."\n":'dots: true,'."\n";
		$custom_js .= ( $params['sm_infinite'] == 0 ) ? 'infinite: false,'."\n":'infinite: true,'."\n";
		$custom_js .= ( $params['sm_variableWidth'] == 0 ) ? 'variableWidth: false,'."\n":'variableWidth: true,'."\n";
		$custom_js .= ( $params['sm_autoplay'] == 0 ) ? 'autoplay: false,'."\n":'autoplay: true,'."\n";
		$custom_js .= ( $params['sm_fade'] == 0 ) ? 'fade: false,'."\n":'fade: true,'."\n";
		$custom_js .= ( $params['sm_centerMode'] == 0 ) ? 'centerMode: false,'."\n":'centerMode: true,'."\n";
		$custom_js .= ( $params['sm_arrows'] == 0 ) ? 'arrows: false,'."\n":'arrows: true,'."\n";
		$custom_js .= ( $params['sm_rtl'] == 0 ) ? 'rtl: false,'."\n":'rtl: true,'."\n";
		
		$custom_js .= 'speed:'.$params['sm_speed'].','."\n";
		$custom_js .= 'centerPadding: \'40px\','."\n";
		$custom_js .= 'autoplaySpeed:'.$params['sm_autoplaySpeed'].','."\n";
		$custom_js .= 'slidesPerRow:'.$params['sm_slidesPerRow'].','."\n";
		$custom_js .= 'slidesToShow:'.$params['sm_slidesToShow'].','."\n";
		$custom_js .= 'slidesToScroll:'.$params['sm_slidesToScroll'].','."\n";
		$custom_js .= "\t".'}'."\n";
		$custom_js .= '},'."\n";
		$custom_js .= '{'."\n";
		$custom_js .= 'breakpoint:'.$params['md_breakpoint'].','."\n";
		$custom_js .= "\t".'settings: {'."\n";
		$custom_js .= ( $params['md_dots'] == 0 ) ? 'dots: false,'."\n":'dots: true,'."\n";
		$custom_js .= ( $params['md_infinite'] == 0 ) ? 'infinite: false,'."\n":'infinite: true,'."\n";
		$custom_js .= ( $params['md_variableWidth'] == 0 ) ? 'variableWidth: false,'."\n":'variableWidth: true,'."\n";
		$custom_js .= ( $params['md_autoplay'] == 0 ) ? 'autoplay: false,'."\n":'autoplay: true,'."\n";
		$custom_js .= ( $params['md_fade'] == 0 ) ? 'fade: false,'."\n":'fade: true,'."\n";
		$custom_js .= ( $params['md_centerMode'] == 0 ) ? 'centerMode: false,'."\n":'centerMode: true,'."\n";
		$custom_js .= ( $params['md_arrows'] == 0 ) ? 'arrows: false,'."\n":'arrows: true,'."\n";
		$custom_js .= ( $params['md_rtl'] == 0 ) ? 'rtl: false,'."\n":'rtl: true,'."\n";
		
		$custom_js .= 'speed:'.$params['md_speed'].','."\n";
		$custom_js .= 'centerPadding: \'40px\','."\n";
		$custom_js .= 'autoplaySpeed:'.$params['md_autoplaySpeed'].','."\n";
		$custom_js .= 'slidesPerRow:'.$params['md_slidesPerRow'].','."\n";
		$custom_js .= 'slidesToShow:'.$params['md_slidesToShow'].','."\n";
		$custom_js .= 'slidesToScroll:'.$params['md_slidesToScroll'].','."\n";
		$custom_js .= "\t".'}'."\n";
		$custom_js .= '},'."\n";
		$custom_js .= '{'."\n";
		$custom_js .= 'breakpoint:'.$params['lg_breakpoint'].','."\n";
		$custom_js .= "\t".'settings: {'."\n";
		$custom_js .= ( $params['lg_dots'] == 0 ) ? 'dots: false,'."\n":'dots: true,'."\n";
		$custom_js .= ( $params['lg_infinite'] == 0 ) ? 'infinite: false,'."\n":'infinite: true,'."\n";
		$custom_js .= ( $params['lg_variableWidth'] == 0 ) ? 'variableWidth: false,'."\n":'variableWidth: true,'."\n";
		$custom_js .= ( $params['lg_autoplay'] == 0 ) ? 'autoplay: false,'."\n":'autoplay: true,'."\n";
		$custom_js .= ( $params['lg_fade'] == 0 ) ? 'fade: false,'."\n":'fade: true,'."\n";
		$custom_js .= ( $params['lg_centerMode'] == 0 ) ? 'centerMode: false,'."\n":'centerMode: true,'."\n";
		$custom_js .= ( $params['lg_arrows'] == 0 ) ? 'arrows: false,'."\n":'arrows: true,'."\n";
		$custom_js .= ( $params['lg_rtl'] == 0 ) ? 'rtl: false,'."\n":'rtl: true,'."\n";
		
		$custom_js .= 'speed:'.$params['lg_speed'].','."\n";
		$custom_js .= 'centerPadding: \'40px\','."\n";
		$custom_js .= 'autoplaySpeed:'.$params['lg_autoplaySpeed'].','."\n";
		$custom_js .= 'slidesPerRow:'.$params['lg_slidesPerRow'].','."\n";
		$custom_js .= 'slidesToShow:'.$params['lg_slidesToShow'].','."\n";
		$custom_js .= 'slidesToScroll:'.$params['lg_slidesToScroll'].','."\n";
		$custom_js .= "\t".'}'."\n";
		$custom_js .= '},'."\n";
		$custom_js .= ']'."\n";
		
		$custom_js .= '});'."\n";
		$custom_js .= '});'."\n";
		$custom_js .= '</script>'."\n\n";
		
		
		$plugin_params = array(
				// This is what will enclose the block in the skin:
				'block_start'       					=> 		"\n".'<section id="slick_'.$this->classname.'_'.$wi_ID.'" class="slider">'."\n",
				'block_end'         					=> 		"\n".'</section>'."\n",
					
				'heading_start'       					=> 		'<h4 class="text-center heading"><!-- heading START -->'."\n",
				'heading_end'         					=> 		"\n".'</h5><!-- heading END -->'."\n",
	
				'subheading_start'       				=> 		'<h5 class="text-center subheading"><!-- subheading START -->'."\n",
				'subheading_end'         				=> 		"\n".'</h5><!-- subheading END -->'."\n",
					
				'item_start' 							=> 		"\n\n".'<div><!--  item START -->'."\n",
				'item_end'  							=> 		"\n".'</div><!-- item END -->',
						
				// Item Image		
				'before_item_no_image' 					=> 		'<div class="center-item" $style$><!-- no image START -->'."\n".'<p class="text-center">',
				'after_item_no_image'   				=> 		'</p>'."\n".'</div><!-- no image END -->',
								
				'before_item_image' 					=> 		"\n".'<a href="$href$" class="$class$" title="$title$" target="$target$"><!-- link START -->'."\n",
				'after_item_image'   					=> 		"\n".'</a><!-- link END -->'."\n",
			
				'class'          						=> 		'center-item',
			);
		
		/* 
		*	The standard is that the skin control params so that plugins will conform to the skin Theme
		*	but perhaps the user want the plugin to define the perams instead
		*/
		if( $params['force'] != 1 )
		{
			// Skin define params
			$params = array_merge( $plugin_params, $params );
			
		}
		else
		{
			// Plugin define params
			$params = array_merge( $params, $plugin_params );
		
		}
			
		$r = '';
		
		$r .= $custom_js;
		$r .= $custom_css;
		$r .= $params['block_start'];
		
		
		if( is_array( $plugin_sets ) )
		{
			// ------ Loop through list of configured Settings: ------
			
			foreach( $plugin_sets as $id => $set )
			{

				if ( array_key_exists('item_disabled', $set ) )
				{

					if( ! empty( $set['item_disabled'] ) )
					{
						//Skipping disabled setting.
						continue;
					}
					
				}
				
				// @See isset( $thumbnail_sizes[ $params['thumb_size'] ] ) 
				$style = 'style="position: relative;display: -moz-inline-block;display:inline-block!important;margin-left:auto;margin-right:auto;width:'.$width.'px!important;height:100%!important;"';
					
				$params['before_image'] = preg_replace('~\$style\$~', $style, $params['before_item_no_image']);
		
				$params['after_image'] = $params['after_item_no_image'];
			
				$r .= $params['item_start'];
				
				$set = array_merge( array( 'class' => $params['class'], 'thumb_size' => $params['thumb_size'] ), $set);

				$image_attrs = $this->get_image_attrib( $set );
				
				if( ! empty( $image_attrs ) )
				{
					
					$params['before_image'] = $params['before_item_image'];

					$params['after_image'] = $params['after_item_image'];
					
					
					if( is_array( $image_attrs ) )
					{
						
						$r .= preg_replace( array('~\$href\$~', '~\$class\$~', '~\$title\$~', '~\$target\$~'), array( $set['href_src'], $set['href_class'], $set['href_title'], $set['href_target'] ), $params['before_image']);
						$r .=  '<img'.get_field_attribs_as_string( $image_attrs ).' />';
						$r .= $params['after_image' ];
						
					}

				}
				else 
				{
					
					$r .= $params['before_image' ];
					$r .= $set[ 'body' ];
					$r .= $params['after_image' ];
				}
				
				// Only display if specified
				if( ! empty( $set['heading'] ))
				{
					$r .= $params[ 'heading_start' ];
					$r .= $set['heading'];
					$r .= $params[ 'heading_end' ];
				}
				// Only display if specified
				if( ! empty( $set['subheading'] ))
				{
					$r .= $params[ 'subheading_start' ];
					$r .= $set['subheading'];
					$r .= $params[ 'subheading_end' ];
				}
				
				$r .= $params['item_end'];	
				
			}
		
		}
		
		$r .= $params['block_end' ];
	
		echo $r;
	}
}
?>
