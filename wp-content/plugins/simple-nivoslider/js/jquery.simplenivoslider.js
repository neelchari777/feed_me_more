/**
 * Simple NivoSlider
 *
 * @package    Simple NivoSlider
 * @subpackage jquery.simplenivoslider.js
/*
	Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

jQuery(
	function() {
		for (var i = 1; i < parseInt( nivo_settings.maxcount ) + 1; i++) {
			jQuery( '#simplenivoslider-' + nivo_settings['id' + i] ).nivoSlider(
				{
					effect: nivo_settings['effect' + i],
					slices: nivo_settings['slices' + i],
					boxCols: parseInt( nivo_settings['boxcols' + i] ),
					boxRows: parseInt( nivo_settings['boxrows' + i] ),
					animSpeed: parseInt( nivo_settings['animspeed' + i] ),
					pauseTime: parseInt( nivo_settings['pausetime' + i] ),
					startSlide: parseInt( nivo_settings['startslide' + i] ),
					directionNav: ! ! nivo_settings['directionnav' + i],
					controlNav: ! ! nivo_settings['controlnav' + i],
					controlNavThumbs: ! ! nivo_settings['controlnavthumbs' + i],
					thumbswidth: parseInt( nivo_settings['thumbswidth' + i] ),
					pauseOnHover: ! ! nivo_settings['pauseonhover' + i],
					manualAdvance: ! ! nivo_settings['manualadvance' + i],
					prevText: nivo_settings['prevtext' + i],
					nextText: nivo_settings['nexttext' + i],
					randomStart: ! ! nivo_settings['randomstart' + i]
				}
			);
		}
	}
);
