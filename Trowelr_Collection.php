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
 * Trowelr Collection Object
 *
 * @author     Fred Ghosn <fredghosn@gmail.com>
 * @license    http://opensource.org/licenses/bsd-license.php BSD License
 * @since      2011-04-19
 */

class Trowelr_Collection {
	protected $sxml = null;
	protected $sources = array();

	public function __construct( $paths = array() ) {
		foreach( $paths as $path ) {
			$this->sources[] = new Trowelr( $path );
		}
	}

	public function getDefinitionByName( $name = '', $recursive = false ) {
		if ( strlen( $name ) == 0 ) {
			return false;
		}

		foreach( $this->sources as $source ) {
			if ( $definition = $source->getDefinitionByName( $name ) ) {
				if ( $recursive == true && strlen( $definition->extends ) ) {
					$definition->extends = $this->getDefinitionByName( $definition->extends, true );
				}

				return $definition;
			}
		}
	}

	public function getFinalDefinition( Trowelr_Definition $definition = null ) {
		
	}
}