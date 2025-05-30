<?php namespace RealTimeAutoFindReplace\admin\builders;

/**
 * From Builder Class
 *
 * @package Builder
 * @author CodeSolz <info@codesolz.com>
 */

if ( ! defined( 'CS_RTAFAR_VERSION' ) ) {
	exit;
}

use RealTimeAutoFindReplace\lib\Util;

class FormBuilder {

	/**
	 * Generate html form fields
	 *
	 * @param type $fields
	 * @return boolean
	 */
	public function generate_html_fields( $fields, $return_array = false ) {
		if ( empty( $fields ) ) {
			return false; }

		$html_fields = array();
		$field_count = \count( $fields );

		$i = 1;
		foreach ( $fields as  $field_name => $field ) {
			$no_border = '';
			$input     = '';
			if ( $field_count == $i ) {
				$no_border = 'no-border';
			}

			$wrapper_class = '';
			if ( isset( $field['wrapper_class'] ) ) {
				$wrapper_class = $field['wrapper_class'];
			}

			if ( isset( $field['type'] ) && $field['type'] == 'section_title' ) {
				$input = $this->form_field_section_title( $field, $wrapper_class );
			} else {

				// if field start with new section
				if ( isset( $field['section'] ) && ! empty( $field['section'] ) ) {
					$input = $field['section'];
				}

				// check if pro
				$is_pro = '';
				if ( isset( $field['is_pro'] ) && true === $field['is_pro'] ) {
					$is_pro = 'pro-version';
				}

				$input .= '<div class="form-group ' . $no_border . ' ' . $wrapper_class . ' ' . $is_pro . '">';
				$input .= $this->generate_field( $field_name, $field, $i );
				$input .= '</div>';

				if ( isset( $field['section'] ) && ! empty( $field['section'] ) ) {
					$input .= '</div>';
				}
			}

			$html_fields[] = $input;
			++$i;
		}

		// return html as string
		if ( false == $return_array ) {
			return \implode( '', $html_fields );
		}

		// return array
		return $html_fields;
	}

	/**
	 * Generate hidden fields
	 *
	 * @param type $fields
	 * @return boolean | string
	 */
	public function generate_hidden_fields( $fields ) {
		if ( empty( $fields ) ) {
			return false;
		}
		$hidden_fields = '';
		$i             = 1;
		foreach ( $fields as  $field_name => $field ) {
			$hidden_fields .= $this->generate_text_field( $field_name, $field, $i );
		}
		return $hidden_fields;
	}

	/**
	 * generate field
	 *
	 * @param type $field
	 */
	private function generate_field( $field_name, $field, $field_id ) {

		$input  = '<div class="label"><label class="label-'.$field_id.'">';
		$input .= $this->generate_title( $field );
		$input .= '</label></div>';
		$input .= '<div class="input-group">';
		if ( isset( $field['input_field_wrap_start'] ) && ! empty( $field['input_field_wrap_start'] ) ) {
			$input .= $field['input_field_wrap_start'];
		}

		if ( $field['type'] == 'text' || $field['type'] == 'email' || $field['type'] == 'number' || $field['type'] == 'password' ) {
			$input .= $this->generate_text_field( $field_name, $field, $field_id );
		}
		if ( $field['type'] == 'textarea' ) {
			$input .= $this->generate_textarea_field( $field_name, $field, $field_id );
		} elseif ( $field['type'] == 'select' ) {
			$input .= $this->generate_select_field( $field_name, $field, $field_id );
		} elseif ( $field['type'] == 'checkbox' ) {
			$input .= $this->generate_checkbox_field( $field_name, $field, $field_id );
		} elseif ( $field['type'] == 'miscellaneous' ) {
			foreach ( $field['options'] as $item_name => $item_assets ) {

				// check has wrapper class
				if ( isset( $field['after_text_wrapper_class'] ) ) {
					$input .= '<span class="' . $field['after_text_wrapper_class'] . '">';
				}

				if ( $item_assets['type'] == 'text' || $field['type'] == 'email' || $item_assets['type'] == 'number' || $item_assets['type'] == 'password' ) {
					$input .= $this->generate_text_field( $item_name, $item_assets, 'mis_' . $field_id );
				} elseif ( $item_assets['type'] == 'select' ) {
					$input .= $this->generate_select_field( $item_name, $item_assets, 'mis_' . $field_id );
				} elseif ( $item_assets['type'] == 'checkbox' ) {
					$input .= $this->generate_checkbox_field( $item_name, $item_assets, 'mis_' . $field_id );
				} elseif ( $item_assets['type'] == 'textarea' ) {
					$input .= $this->generate_textarea_field( $item_name, $item_assets, 'mis_' . $field_id );
				}

				if ( isset( $item_assets['after_text'] ) ) {
					$input .= $item_assets['after_text'];
				}

				if ( isset( $field['after_text_wrapper_class'] ) ) {
					$input .= '</span>';
				}
			}
		}

		if ( isset( $field['input_field_wrap_end'] ) && ! empty( $field['input_field_wrap_end'] ) ) {
			$input .= $field['input_field_wrap_end'];
		}

		// if no hidden fields show tip before hidden items
		if ( isset( $field['desc_tip'] ) && ! empty( $field['desc_tip'] ) && ! isset( $field['hidden_div'] ) ) {
			$input .= '<p class="description">' . $field['desc_tip'] . '</p>';
		}

		if ( isset( $field['hidden_div'] ) ) {
			$input .= $this->generate_hidden_div( $field_name, $field['hidden_div'], $field_id );
		}

		// if hidden fields show tip after hidden items
		if ( isset( $field['desc_tip'] ) && ! empty( $field['desc_tip'] ) && isset( $field['hidden_div'] ) ) {
			$input .= '<p class="description">' . $field['desc_tip'] . '</p>';
		}

		$input .= '</div>';

		return $input;
	}

	/**
	 * Generate section title
	 *
	 * @param type $args
	 * @return boolean
	 */
	public function form_field_section_title( $args, $wrapper_class ) {
		$section_title = '<div class="section-title ' . $wrapper_class . ' ">' . sprintf( $args['title'], '<i class="fa fa-edit"></i> ' ) . '</div>';
		if ( isset( $args['desc_tip'] ) && ! empty( $args['desc_tip'] ) ) {
			$section_title .= '<p class="section-description ' . $wrapper_class . ' ">' . $args['desc_tip'] . '</p>';
		}
		return $section_title;
	}

	/**
	 * Generate alert div
	 *
	 * @param type $args
	 * @return string
	 */
	private function generate_hidden_div( $field_name, $args, $fields_number ) {
		$attributes = '';
		if ( isset( $args['attributes'] ) ) {
			foreach ( $args['attributes'] as $attr_key => $attr_val ) {
				if ( $attr_key == 'id' ) {
					$attr_val = $attr_val . '_' . $fields_number;
				}
				$attributes .= ' ' . $attr_key . '="' . $attr_val . '" ';
			}
		}

		$input = '';
		if ( isset( $args['more_input_fields'] ) && ! empty( $more_input_fields = $args['more_input_fields'] ) ) {
			$field = $more_input_fields['attributes'];
			for ( $i = 1; $i <= $more_input_fields['item']; $i++ ) {
				$field['value'] = isset( $more_input_fields['values'][ $i ] ) ? $more_input_fields['values'][ $i ] : '';
				if ( $field_name == 'cs_add_new[coin_address]' ) {
					$field_name = 'more_coin_address[]';
				}
				$input .= $this->generate_text_field( $field_name, $field, $fields_number . '_' . $i );
			}
		}

		$inner_html = isset( $args['inner_html'] ) ? $args['inner_html'] : '';
		return "<div {$attributes}> {$input} {$inner_html}</div>";
	}

	/**
	 * Generate attribute
	 *
	 * @param type $field_name
	 * @param type $field
	 * @param type $field_id
	 * @return type
	 */
	private function generate_attribute( $field_name, $field, $field_id ) {
		// pre_print($field);
		$input_item = '';
		foreach ( $field as $item_id => $item_val ) {
			if ( $field['type'] == 'select' && ( $item_id == 'placeholder' || $item_id == 'type' ) ) {
				continue;
			}

			if ( $field['type'] == 'textarea' && ( $item_id == 'value' || $item_id == 'type' ) ) {
				continue;
			}

			if ( \method_exists( $this, ( $method = 'attr_' . $item_id ) ) ) {
				$input_item .= $this->$method( $item_val );
			}
			if ( $item_id == 'custom_attributes' ) {
				foreach ( $item_val  as  $cs_attr_name => $cs_attr_val ) {
					$input_item .= " $cs_attr_name = '$cs_attr_val' ";
				}
			}
		}
		$input_item .= $this->attr_name( $field_name );
		$input_item .= $this->attr_id( $field_id );

		return $input_item;
	}

	/**
	 * get disabled fields val
	 */
	private function get_hidden_field_val( $field_name, $field, $field_id ) {
		if ( isset( $field['disabled'] ) && true === $field['disabled'] ) {
			$input_value = $field['value'];
			return '<input type="hidden" value ="' . $input_value . '" name ="' . $field_name . '" />';
		}
		return false;
	}

	/**
	 * Generate text filed
	 *
	 * @param type $field_name
	 * @param type $field
	 * @param type $field_id
	 * @return type
	 */
	private function generate_text_field( $field_name, $field, $field_id ) {
		$input_item         = $this->generate_attribute( $field_name, $field, $field_id );
		$disabled_field_val = $this->get_hidden_field_val( $field_name, $field, $field_id );
		return "<input  {$input_item} /> {$disabled_field_val}";
	}

	/**
	 * Generate textarea filed
	 *
	 * @param type $field_name
	 * @param type $field
	 * @param type $field_id
	 * @return type
	 */
	private function generate_textarea_field( $field_name, $field, $field_id ) {
		$input_item = $this->generate_attribute( $field_name, $field, $field_id );
		return "<textarea  {$input_item} >" . $field['value'] . '</textarea>';
	}

	/**
	 * Generate text filed
	 *
	 * @param type $field_name
	 * @param type $field
	 * @param type $field_id
	 * @return type
	 */
	private function generate_checkbox_field( $field_name, $field, $field_id ) {
		$value = '';
		if ( isset( $field['has_value'] ) && ! empty( $field['has_value'] ) ) {
			$value = ' checked = "checked" ';
		}

		// old fields
		if ( ! isset( $field['has_value'] ) && isset( $field['value'] ) && ! empty( $field['value'] ) ) {
			$value = $field['value'] == 1 || $field['value'] == 'on' ? ' checked = "checked" ' : '';
			unset( $field['value'] );
		}

		$input_item = $this->generate_attribute( $field_name, $field, $field_id );

		$label = '';
		if ( isset( $field['label'] ) ) {

			$is_disabled = '';
			if ( isset( $field['disabled'] ) ) {
				$is_disabled = 'checkbox-disabled-label';
			}

			$label = '<span class="checkbox-label ' . $is_disabled . '">' . $field['label'] . '</span>';
		}

		return "<input  {$input_item} {$value}/> {$label} ";
	}

	/**
	 * Generate select field
	 *
	 * @param type $field_name
	 * @param type $field
	 * @param type $field_id
	 * @return string
	 */
	private function generate_select_field( $field_name, $field, $field_id ) {
		$value = '';
		if ( isset( $field['value'] ) && ! empty( $field['value'] ) ) {
			$value = $field['value'];
			unset( $field['value'] );
		}

		// pre_print( $field );

		$cus_val            = $field;
		$cus_val['value']   = $value;
		$disabled_field_val = $this->get_hidden_field_val( $field_name, $cus_val, $field_id );

		$input_item = $this->generate_attribute( $field_name, $field, $field_id );
		$tag_start  = "<select  {$input_item} >";
		// $option     = '<option value="" disabled class="placeholder" >==================== ' . $field['placeholder'] . ' ====================</option>';
		$option = '';
		if ( ! empty( $field['options'] ) ) {

			if ( isset( $field['options']['hasGroup'] ) ) {
				foreach ( $field['options']['hasGroup'] as $groupName => $groupOptions ) {
					$placeholder_text = sprintf( $field['placeholder'], \strtolower( $groupName ) );
					$option          .= '<optgroup label="' . $groupName . '">' . $this->generate_select_options( $groupOptions, $placeholder_text, $value ) . '</optgroup>';
				}
			} else {
				$option = $this->generate_select_options( $field['options'], $field['placeholder'], $value );

			}
		}
		$select_tag = $tag_start . $option . '</select>';

		return $select_tag . $disabled_field_val;
	}

	/**
	 * Generate select options
	 *
	 * @return void
	 */
	private function generate_select_options( $options, $placeholder_text, $selected_item ) {
		if ( empty( $options ) ) {
			return '<option value ="" >' . __( 'No option available', 'real-time-auto-find-and-replace' ) . '</option>';
		}

		// $item = '';
		$item = '<option value="" disabled class="placeholder" >==================== ' . $placeholder_text . ' ====================</option>';
		foreach ( $options as $key => $val ) {
			$selected = '';
			if ( ( is_array( $selected_item ) && in_array( $key, $selected_item ) ) || $key == $selected_item ) {
				$selected = 'selected="selected"';
			}

			$disabled = '';
			if ( \strpos( $key, '_disabled' ) !== false ) {
				$disabled = 'disabled';
			}

			$item .= '<option value ="' . $key . '" ' . $selected . ' ' . $disabled . ' >' . $val . '</option>';
		}

		return $item;
	}

	/**
	 * Generate title
	 */
	private function generate_title( $field ) {
		return isset( $field['title'] ) ? $field['title'] : '&nbsp';
	}

	/**
	 * Attr type
	 *
	 * @param type $type
	 * @return type
	 */
	private function attr_type( $type ) {
		return ' type = "' . $type . '"';
	}

	/**
	 * attr class
	 *
	 * @param type $class
	 * @return string
	 */
	private function attr_class( $class ) {
		return ' class = "' . $class . '" ';
	}

	/**
	 * attr placeholder
	 *
	 * @param type $placeholder
	 * @return string
	 */
	private function attr_placeholder( $placeholder ) {
		return ' placeholder = "' . $placeholder . '" ';
	}

	/**
	 * attr value
	 *
	 * @param type $value
	 * @return string
	 */
	private function attr_value( $value ) {
		if ( is_array( $value ) ) {
			return ' value = "invalid value" ';
		}

		return ' value = "' . $value . '" ';
	}

	/**
	 * attr disable
	 *
	 * @param type $item
	 * @return string
	 */
	private function attr_disabled( $val ) {
		if ( true === $val ) {
			return ' disabled = "disabled" ';
		}
	}

	/**
	 * attr required
	 *
	 * @param type $item
	 * @return string
	 */
	private function attr_required( $val ) {
		if ( true === $val ) {
			return ' required = "required" ';
		}
	}

	/**
	 * attr readonly
	 *
	 * @param type $item
	 * @return string
	 */
	private function attr_readonly( $val ) {
		if ( true === $val ) {
			return ' readonly ';
		}
	}

	/**
	 * attr name
	 *
	 * @param type $field_name
	 * @return string
	 */
	private function attr_name( $field_name ) {
		return ' name = "' . $field_name . '" ';
	}

	/**
	 * attr name
	 *
	 * @param type $field_id
	 * @return string
	 */
	private function attr_id( $field_id ) {
		return ' id = "cs_field_' . $field_id . '" ';
	}

	/**
	 * attr multiple
	 *
	 * @param type $field_id
	 * @return string
	 */
	private function attr_multiple( $field_id ) {
		return ' multiple = "' . $field_id . '" ';
	}

	/**
	 *
	 * @param type $id
	 * @param type $values
	 * @param type $default_value
	 * @return stringGet field's value
	 */
	public static function get_value( $id, $values = array(), $default_value = '' ) {
		if ( isset( $values[ $id ] ) && ! empty( $values[ $id ] ) ) {
			return $values[ $id ];
		} elseif ( ! empty( $default_value ) ) {
			return Util::cs_esc_html( $default_value );
		}
		return '';
	}
}
