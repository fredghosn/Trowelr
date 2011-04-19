<?php
############################################################################
#    Copyright (C) 2011 by Fred Ghosn                                      #
#    fredghosn@gmail.com                                                   #
#                                                                          #
#    Permission is hereby granted, free of charge, to any person obtaining #
#    a copy of this software and associated documentation files (the       #
#    "Software"), to deal in the Software without restriction, including   #
#    without limitation the rights to use, copy, modify, merge, publish,   #
#    distribute, sublicense, and#or sell copies of the Software, and to    #
#    permit persons to whom the Software is furnished to do so, subject to #
#    the following conditions:                                             #
#                                                                          #
#    The above copyright notice and this permission notice shall be        #
#    included in all copies or substantial portions of the Software.       #
#                                                                          #
#    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       #
#    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    #
#    MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.#
#    IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     #
#    OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, #
#    ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR #
#    OTHER DEALINGS IN THE SOFTWARE.                                       #
############################################################################

/**
 * Trowelr Object
 *
 * @author     Fred Ghosn <fredghosn@gmail.com>
 * @license    http://opensource.org/licenses/bsd-license.php BSD License
 * @since      2011-04-19
 */

require_once( 'Trowelr_Collection.php' );
require_once( 'Trowelr_Definition.php' );
require_once( 'Trowelr_AttributeList.php' );
require_once( 'Trowelr_Attribute.php' );

class Trowelr {
	public $sxml;
	private $definitions;

	public function __construct( $path = '' ) {
		$this->sxml = simplexml_load_file( $path );

		foreach( $this->sxml->definition as $definition ) {
			$this->add( $this->createDefinition( $definition ) );
		}
	}

	public function add( Trowelr_Definition $definition = null ) {
		if ( $definition != null ) {
			$this->definitions[] = $definition;
		}
	}

	public function remove( Trowelr_Definition $definition = null ) {
		for( $i = 0; $i < sizeof( $this->definitions ); $i++ ) {
			if ( $this->definitions[$i] === $definition ) {
				unset( $this->definitions[$i] );
				return true;
			}
		}
		return false;
	}

	public function getDefinitionByName( $name = '' ) {
		if ( strlen( $name ) == 0 ) {
			return false;
		}

		foreach( $this->definitions as $definition ) {
			if ( $definition->name == $name ) {
				return $definition;
			}
		}
	}

	private function createDefinition( SimpleXMLElement $xml ) {
		$definition = new Trowelr_Definition( strval( $xml['name'] ) );
		$definition->template = strval( $xml['template'] );
		$definition->preparer = strval( $xml['preparer'] );
		$definition->extends  = strval( $xml['extends'] );

		foreach( $xml->{'put-attribute'} as $item )
			$definition->add( $this->createAttribute( $item ) );

		foreach( $xml->{'put-list-attribute'} as $item )
			$definition->add( $this->createAttribute( $item ) );

		return $definition;
	}

	private function createAttribute( SimpleXMLElement $xml ) {
		if ( $xml->getName() == 'put-attribute' ) {
			if ( sizeof( $xml->definition ) ) {
				$value = $this->createDefinition( $xml->definition );
			} else {
				$value = strval( $xml['value'] );
			}

			$attribute = new Trowelr_Attribute( strval( $xml['name'] ), $value, strval( $xml['cascade'] ), strval( $xml['inherit'] ) );
		} else if ( $xml->getName() == 'put-list-attribute' ) {
			$attribute = new Trowelr_AttributeList( strval( $xml['name'] ), strval( $xml['cascade'] ), strval( $xml['inherit'] ) );

			foreach( $xml->{'add-attribute'} as $item ) {
				if ( sizeof( $item->definition ) ) {
					$value = $this->createDefinition( $item );
				} else {
					$value = strval( $item['value'] );
				}

				$attribute->add( strval( $item['name'] ), $value );
			}
		}

		return $attribute;
	}
}