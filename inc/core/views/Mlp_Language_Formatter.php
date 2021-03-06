<?php # -*- coding: utf-8 -*-
class Mlp_Language_Formatter {

	protected $raw_data, $type;
	/**
	 * Constructor.
	 */
	public function __construct( stdClass $raw_data, $type = 'form' )
	{
		$this->raw_data = $raw_data;
		$this->type     = $type;
	}

	public function __get( $name ) {

		if ( 'form' !== $this->type )
			return $this->get_content( $name );

		return $this->get_form_element( $name );
	}

	public function get_content( $name ) {

		if ( 'short_name' === $name )
			return $this->get_short_name();

		if ( 'is_rtl' === $name )
			return empty ( $this->raw_data->is_rtl ) ? 0 : $this->raw_data->is_rtl;

		if ( isset ( $this->raw_data->$name ) )
			return $this->raw_data->$name;

		if ( 'ID' === $name ) // new language
			return 0;

		return '';
	}

	public function get_form_element( $name ) {

		$id   = (int) $this->get_content( 'ID' );
		$size = $this->get_size( $name );

		if ( 'checkbox' === $name )
			return "<input type='checkbox' id='lang_$id' name='delete_languages[]' value='$id' />";

		$content = esc_attr( $this->get_content( $name ) );

		if ( 'priority' === $name )
			return "<input type='number' name='languages[$id][is_rtl]' value='$content' min=1 max=10 size=3 />";

		if ( 'is_rtl' === $name )
			return $this->get_rtl_checkbox( $content, $id );

		return "<input type='text' name='languages[$id]" . "[$name]" . "' value='" . $content . "' $size />";
	}

	protected function get_rtl_checkbox( $value, $id ) {
		return sprintf(
			'<input type="checkbox" name="languages[%1$d][is_rtl]" value="1" %2$s />',
			$id,
			checked( $value, 1, FALSE )
		);
	}

	protected function get_short_name() {

		if ( ! empty ( $this->raw_data->short_name ) )
			return $this->raw_data->short_name;

		if ( ! empty ( $this->raw_data->iso_639_1 ) )
			return strtok( $this->raw_data->iso_639_1, '_' );

		return '';
	}

	protected function get_size( $name ) {

		$num = 5;

		if ( in_array( $name, array ( 'english_name', 'native_name', 'custom_name' ) ) )
			$num = 20;

		if ( in_array( $name, array ( 'text_direction', 'iso_639_2' ) ) )
			$num = 3;

		return " size='$num'";
	}
}