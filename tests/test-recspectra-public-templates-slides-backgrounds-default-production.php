<?php
/**
 * @group theater
 */
class Test_Recspectra_Public_Templates_Slides_Backgrounds_Default_Production extends Recspectra_Theater_UnitTestCase {

	/**
	 * @since	1.4.0
	 */
	function test_are_all_default_background_properties_of_production_slide_included_in_slide() {

		$this->assume_role( 'administrator' );

		$production_title = 'Superduperproductionwiththumb';
		$prod_args = array(
			'post_type' => WPT_Production::post_type_name,
			'post_title' => $production_title,
		);

		/* Create production */
		$production_id = $this->factory->post->create( $prod_args );

		/* Create image attachment and set as production thumbnail */
		$file = dirname( __FILE__ ) . '/assets/Kip-400x400.jpg';
		$image_attachment_id = $this->factory->attachment->create_upload_object( $file );
		set_post_thumbnail( $production_id, $image_attachment_id );

		update_post_meta( $this->slide1, 'slide_format', 'production' );
		update_post_meta( $this->slide1, 'slide_background', 'default' );
		update_post_meta( $this->slide1, 'slide_production_production_id', $production_id );

		$this->go_to( get_permalink( $this->slide1 ) );

		ob_start();
		Recspectra_Templates::get_template('partials/slide.php');
		$actual = ob_get_clean();

		$this->assertRegExp( '/Kip-400x400.*\.jpg/', $actual );
	}
}

