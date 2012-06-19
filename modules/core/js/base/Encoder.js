/**
 * NextCMS
 * 
 * All the encoding methods below are taken from phpjs
 * 
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	js
 * @since		1.0
 * @version		2011-10-23
 */

dojo.provide("core.js.base.Encoder");

core.js.base.Encoder.encode = function(/*Object*/ object, /*Array?*/ properties) {
	// summary:
	//		Encodes an object using toJson(), encodeBase64() and rawEncodeUrl() method
	// object:
	//		Object to encode
	// properties:
	//		The array of object's properties which will be encoded.
	//		If null, all the object's properties will be encoded
	var data = {};
	if (properties) {
		dojo.forEach(properties, function(name) {
			data[name] = object[name];
		});
	} else {
		data = object;
	}
	
	var encodedString = dojo.toJson(data);
	encodedString = core.js.base.Encoder.encodeBase64(encodedString);
	encodedString = core.js.base.Encoder.rawEncodeUrl(encodedString);
	return encodedString;	// String
};

core.js.base.Encoder.decode = function(/*String*/ encodedString) {
	// summary:
	//		Decodes an encoded string which is encoded by the core.js.base.Encoder.encode() method
	var object = core.js.base.Encoder.rawDecodeUrl(encodedString);
	object = core.js.base.Encoder.decodeBase64(object);
	object = dojo.fromJson(object);
	return object;	// Object
};

core.js.base.Encoder.encodeUtf8 = function(/*String*/ str) {
	// summary:
	//		Encodes an ISO-8859-1 string to UTF-8
	// description:
	//		See http://phpjs.org/functions/utf8_encode
	// author:
	//		- http://www.webtoolkit.info
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- sowberry
	//		- Jack
	//		- Onno Marsman
	//		- Yves Sucaet
	//		- Ulrich
	var string  = (str + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	var utftext = "", start, end, stringl = 0;

	start   = end = 0;
	stringl = string.length;
	for ( var n = 0; n < stringl; n++) {
		var c1 = string.charCodeAt(n);
		var enc = null;

		if (c1 < 128) {
			end++;
		} else if (c1 > 127 && c1 < 2048) {
			enc = String.fromCharCode((c1 >> 6) | 192)
					+ String.fromCharCode((c1 & 63) | 128);
		} else {
			enc = String.fromCharCode((c1 >> 12) | 224)
					+ String.fromCharCode(((c1 >> 6) & 63) | 128)
					+ String.fromCharCode((c1 & 63) | 128);
		}
		if (enc !== null) {
			if (end > start) {
				utftext += string.slice(start, end);
			}
			utftext += enc;
			start = end = n + 1;
		}
	}

	if (end > start) {
		utftext += string.slice(start, stringl);
	}

	return utftext;		// String
};

core.js.base.Encoder.decodeUtf8 = function(/*String*/ str) {
	// summary:
	//		Converts a UTF-8 encoded string to ISO-8859-1
	// description:
	//		See http://phpjs.org/functions/utf8_decode
	// author:
	//		- http://www.webtoolkit.info/
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- Norman "zEh" Fuchs
	//		- hitwork
	//		- Onno Marsman
	//		- Brett Zamir (http://brett-zamir.me)
	var tmpArr = [],
		i = 0,
		ac = 0,
		c1 = 0,
		c2 = 0,
		c3 = 0;
	
	str += '';
	
	while (i < str.length) {
		c1 = str.charCodeAt(i);
		if (c1 < 128) {
			tmpArr[ac++] = String.fromCharCode(c1);
			i++;
		} else if (c1 > 191 && c1 < 224) {
			c2 = str.charCodeAt(i + 1);
			tmpArr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = str.charCodeAt(i + 1);
			c3 = str.charCodeAt(i + 2);
			tmpArr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}
	
	return tmpArr.join('');		// String
};

core.js.base.Encoder.encodeBase64 = function(/*String*/ data) {
	// summary:
	//		Encodes string using MIME base64 algorithm
	// description:
	//		See http://phpjs.org/functions/base64_encode
	// author:
	//		- Tyler Akins (http://rumkin.com)
	//		- Bayron Guevara
	//		- Thunder.m
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- Pellentesque Malesuada
	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		ac = 0,
		enc = "",
		tmpArr = [];

	if (!data) {
		return data;
	}

	data = core.js.base.Encoder.encodeUtf8(data + '');

	do { // pack three octets into four hexets
		o1 = data.charCodeAt(i++);
		o2 = data.charCodeAt(i++);
		o3 = data.charCodeAt(i++);

		bits = o1 << 16 | o2 << 8 | o3;

		h1 = bits >> 18 & 0x3f;
		h2 = bits >> 12 & 0x3f;
		h3 = bits >> 6 & 0x3f;
		h4 = bits & 0x3f;

		// use hexets to index into b64, and append result to encoded string
		tmpArr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
	} while (i < data.length);

	enc = tmpArr.join('');

	switch (data.length % 3) {
		case 1:
			enc = enc.slice(0, -2) + '==';
			break;
		case 2:
			enc = enc.slice(0, -1) + '=';
			break;
	}

	return enc;		// String
};

core.js.base.Encoder.decodeBase64 = function(/*String*/ data) {
	// summary:
	//		Decodes string using MIME base64 algorithm
	// description:
	//		See http://phpjs.org/functions/base64_decode
	// author:
	//		- Tyler Akins (http://rumkin.com)
	//		- Thunder.m
	//		- Aman Gupta
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- Onno Marsman
	//		- Pellentesque Malesuada
	//		- Brett Zamir (http://brett-zamir.me)
	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		ac = 0,
		dec = "",
		tmpArr = [];

	if (!data) {
		return data;
	}

	data += '';

	do { // unpack four hexets into three octets using index points in b64
		h1 = b64.indexOf(data.charAt(i++));
		h2 = b64.indexOf(data.charAt(i++));
		h3 = b64.indexOf(data.charAt(i++));
		h4 = b64.indexOf(data.charAt(i++));

		bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

		o1 = bits >> 16 & 0xff;
		o2 = bits >> 8 & 0xff;
		o3 = bits & 0xff;

		if (h3 == 64) {
			tmpArr[ac++] = String.fromCharCode(o1);
		} else if (h4 == 64) {
			tmpArr[ac++] = String.fromCharCode(o1, o2);
		} else {
			tmpArr[ac++] = String.fromCharCode(o1, o2, o3);
		}
	} while (i < data.length);

	dec = tmpArr.join('');
	dec = core.js.base.Encoder.decodeUtf8(dec);

	return dec;		// String
};

core.js.base.Encoder.encodeUrl = function(/*String*/ url) {
	// summary:
	//		URL-encodes string
	// description:
	//		See http://phpjs.org/functions/urlencode
	// author:
	//		- Philip Peterson
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- AJ
	//		- Brett Zamir (http://brett-zamir.me)
	//		- travc
	//		- Lars Fischer
	//		- Ratheous
	//		- Joris
	url = (url + '').toString();

	// Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
	// PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
	return encodeURIComponent(url).replace(/!/g, '%21')
								  .replace(/'/g, '%27')
								  .replace(/\(/g, '%28')
								  .replace(/\)/g, '%29')
								  .replace(/\*/g, '%2A')
								  .replace(/%20/g, '+');	// String
};

core.js.base.Encoder.decodeUrl = function(/*String*/ url) {
	// summary:
	//		Decodes URL-encoded string
	// description:
	//		See http://phpjs.org/functions/urldecode
	// author:
	//		- Philip Peterson
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- AJ
	//		- Brett Zamir (http://brett-zamir.me)
	//		- travc
	//		- Lars Fischer
	//		- Ratheous
	//		- Orlando
	//		- Rob
	//		- e-mike
	return decodeURIComponent((url + '').replace(/\+/g, '%20'));	// String
};

core.js.base.Encoder.rawEncodeUrl = function(/*String*/ url) {
	// summary:
	//		URL-encodes string
	// description:
	//		See http://phpjs.org/functions/rawurlencode
	// author:
	//		- Brett Zamir (http://brett-zamir.me)
	//		- travc
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- Michael Grier
	//		- Ratheous
	//		- Joris
	url = (url + '').toString();
	
	// Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
	// PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
	return encodeURIComponent(url).replace(/!/g, '%21')
								  .replace(/'/g, '%27')
								  .replace(/\(/g, '%28')
								  .replace(/\)/g, '%29')
								  .replace(/\*/g, '%2A');	// String
};

core.js.base.Encoder.rawDecodeUrl = function(/*String*/ url) {
	// summary:
	//		Decodes URL-encodes string
	// description:
	//		See http://phpjs.org/functions/rawurldecode
	// author:
	//		- Brett Zamir (http://brett-zamir.me)
	//		- travc
	//		- Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//		- Ratheous
	return decodeURIComponent(url + '');	// String
};
