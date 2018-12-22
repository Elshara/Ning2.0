dojo.provide('xg.index.invitation.zColor');

/**
 * Color manipulation library, used to pick colors for the inserted divs in xg.index.invitation.pageLayout
 */
xg.index.invitation.zColor = {

/*------------------------------------------------------------------------------
 * JavaScript zColor Library
 * Version 0.1
 * by Nicholas C. Zakas, http://www.nczonline.net/
 * Copyright (c) 2004-2005 Nicholas C. Zakas. All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 *------------------------------------------------------------------------------
 */


/**
 * Represents an RGB color.
 * @class
 * @scope public
 * @constructor
 * @param iRed The red value for the color (0-255)
 * @param iGreen The green value for the color (0-255)
 * @param iBlue The blue value for the color (0-255)
 */
	zRGB: function(iRed, iGreen, iBlue) {

		/**
		 * The red value for the color.
		 * @scope public
		 */
		this.r /*:int */ = parseInt(iRed,10);

		/**
		 * The green value for the color.
		 * @scope public
		 */
		this.g /*:int */ = parseInt(iGreen,10);

		/**
		 * The blue value for the color.
		 * @scope public
		 */
		this.b /*:int */= parseInt(iBlue,10);
	},

	/**
	 * Represents an HSL color.
	 * @class
	 * @scope public
	 * @constructor
	 * @param iHue The hue value for the color (0-255)
	 * @param iSat The saturation value for the color (0-255)
	 * @param iLum The luminosity value for the color (0-255)
	 */
	zHSL: function (iHue, iSat, iLum) {

		/**
		 * The hue value for the color.
		 * @scope public
		 */
		this.h /*:int */ = parseInt(iHue,10);

		/**
		 * The saturation value for the color.
		 * @scope public
		 */
		this.s /*:int */ = parseInt(iSat,10);

		/**
		 * The luminosity value for the color.
		 * @scope public
		 */
		this.l /*:int */= parseInt(iLum,10);
	}

}


/**
 * Converts the color into RGB form.
 * @scope public
 * @return An RGB version of the color.
 */
xg.index.invitation.zColor.zHSL.prototype.toRGB = function () /*:zRGB */ {

	iHue = this.h/255;
	iSat = this.s/255;
	iLum = this.l/255;

	var iRed, iGreen, iBlue;

	if (iSat == 0) {
		iRed = iGreen = iBlue = iLum;
	} else {

		var m1, m2;

		if (iLum <= 0.5) {
			m2 = iLum * (1+iSat);
		} else {
			m2 = iLum + iSat - (iLum * iSat);
		}

		m1 = 2.0 * iLum - m2;

		hf2 = iHue + 1/3;
		if (hf2 < 0) hf2 = hf2 + 1;
		if (hf2 > 1) hf2 = hf2 - 1;
		if (6 * hf2 < 1) {
			iRed = (m1+(m2-m1)*hf2*6);
		} else {
			if (2 * hf2 < 1) {
				iRed = m2;
			} else {
				if (3.0*hf2 < 2.0) {
					iRed = (m1+(m2-m1)*((2.0/3.0)-hf2)*6.0);
				} else {
					iRed = m1;
				}
			}
		}

		//Calculate Green
		if (iHue < 0) {iHue = iHue + 1.0;}
		if (iHue > 1) {iHue = iHue - 1.0;}
		if (6.0 * iHue < 1){
			iGreen = (m1+(m2-m1)*iHue*6.0);}
		else {
			if (2.0 * iHue < 1){
				iGreen = m2;
			} else {
				if (3.0*iHue < 2.0) {
					iGreen = (m1+(m2-m1)*((2.0/3.0)-iHue)*6.0);
				} else {
					iGreen = m1;
				}
			}
		}

		//Calculate Blue
		hf2=iHue-1.0/3.0;
		if (hf2 < 0) {hf2 = hf2 + 1.0;}
		if (hf2 > 1) {hf2 = hf2 - 1.0;}
		if (6.0 * hf2 < 1) {
			iBlue = (m1+(m2-m1)*hf2*6.0);
		} else {
			if (2.0 * hf2 < 1){
				iBlue = m2;
			} else {
				if (3.0*hf2 < 2.0) {
					iBlue = (m1+(m2-m1)*((2.0/3.0)-hf2)*6.0);
				} else {
					iBlue = m1;
				}
			}
		}

	}
	return new xg.index.invitation.zColor.zRGB(Math.round(iRed*255),Math.round(iGreen*255),Math.round(iBlue*255));
};

/**
 * Returns the color in a string form.
 * @scope public
 * @return The color in a string form.
 */
xg.index.invitation.zColor.zHSL.prototype.toString = function () /*:String */ {
	return "hsl(" + this.h + "," + this.s + "," + this.l + ")";
};


/**
 * Creates an RGB color from a hex string.
 * @scope public
 * @param sHex The hex string.
 * @return The RGB object for the hex string.
 */
xg.index.invitation.zColor.zRGB.fromHexString = function (sHex /*: String */) /*:zRGB */ {

	//eliminate the pound sign
	if (sHex.charAt(0) == "#") {
		sHex = sHex.substring(1);
	} //ENd: if (sHex.charAt(0) == "#")

	//extract and convert the red, green, and blue values
	var iRed = parseInt(sHex.substring(0,2),16);
	var iGreen = parseInt(sHex.substring(2,4),16);
	var iBlue = parseInt(sHex.substring(4,6),16);

	//return an array
	return new xg.index.invitation.zColor.zRGB(iRed,iGreen,iBlue);
};

/**
 * Returns a hex representation of the color.
 * @scope public
 * @return A hex representation of the color.
 */
xg.index.invitation.zColor.zRGB.prototype.toHexString = function () /*:String */ {

	//extract and convert the red, green, and blue values
	var sRed = this.r.toString(16).toUpperCase();
	var sGreen = this.g.toString(16).toUpperCase();
	var sBlue = this.b.toString(16).toUpperCase();

	//make sure there are two digits in each code
	if (sRed.length == 1) {
		sRed = "0" + sRed;
	} //End: if (sRed.length == 1)
	if (sGreen.length == 1) {
		sGreen = "0" + sGreen;
	} //End: if (sGreen.length == 1)
	if (sBlue.length == 1) {
		sBlue = "0" + sBlue;
	} //End: if (sBlue.length == 1)

	//return the hex code
	return "#" + sRed + sGreen + sBlue;
};

/**
 * Returns an HSL representation of the color.
 * @scope public
 * @return An HSL representation of the color.
 */
xg.index.invitation.zColor.zRGB.prototype.toHSL = function () /*:zHSL */ {

	var iMax = Math.max(this.r, this.g, this.b);
	var iMin = Math.min(this.r, this.g, this.b);
	var iDelta = iMax-iMin;

	var iLum = Math.round((iMax+iMin)/2);
	var iHue = 0;
	var iSat = 0;

	if (iDelta > 0) {
		iSat = Math.ceil(((iLum < (0.5*255)) ? iDelta/(iMax + iMin) : iDelta/((2*255)-iMax-iMin))*255);

		var iRedC = (iMax-this.r)/iDelta;
		var iGreenC = (iMax-this.g)/iDelta;
		var iBlueC = (iMax-this.b)/iDelta;

		if (this.r == iMax) {
			iHue = iBlueC - iGreenC;
		} else if (this.g == iMax) {
			iHue = 2.0 + iRedC - iBlueC;
		} else {
			iHue = 4.0 + iGreenC - iRedC;
		}

		iHue /= 6.0;

		if (iHue < 0) {
			iHue += 1.0;
		}

		iHue = Math.round(iHue * 255);
	}

	return new xg.index.invitation.zColor.zHSL(iHue,iSat,iLum);
};

/**
 * Returns the color in a string form.
 * @scope public
 * @return The color in a string form.
 */
xg.index.invitation.zColor.zRGB.prototype.toString = function () /*:String */ {
	return "rgb(" + this.r + "," + this.g + "," + this.b + ")";
};
