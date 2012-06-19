/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	js
 * @since		1.0
 * @version		2011-11-27
 */

dojo.provide("file.js.views.FileFormatter");

file.js.views.FileFormatter.formatPermissions = function(/*String*/ perms) {
	// summary:
	//		Formats file permissions
	// description:
	//		Returns file permissions in string format (like rwxr--r--, for example)
	// perms:
	//		File permissions in the numeric format (777, for example)
	perms = String(perms);
	if (perms.length < 3) {
		return "---------"; // 9 characters of -
	}
	var result = "";
	for (var i = 0; i < perms.length; i++) {
		var p   = parseInt(perms[i]);
		result += (p & 04) ? "r" : "-";		// Read permission
		result += (p & 02) ? "w" : "-";		// Write
		result += (p & 01) ? "x" : "-";		// Execute
	}
	
	return result;
};

file.js.views.FileFormatter.formatSize = function(/*Integer*/ size, /*String*/ zeroString) {
	// summary:
	//		Formats file size. Returns the file size in larger unit, such as MB, GB, etc
	// size:
	//		File size in bytes
	// zeroString:
	//		The string that will be returned if the file size is 0 byte
	if (size == 0) {
		return (zeroString == null) ? "" : zeroString;
	}
	
	var format = file.js.views.FileFormatter._formatNumber;
	switch (true) {
		case (size >= 1073741824):
			return format(size / 1073741824, 2, '.', '') + ' Gb';	// String
			break;
		case (size >= 1048576):
			return format(size / 1048576, 2, '.', '') + ' Mb';	// String
			break;
		case (size >= 1024):
			return format(size / 1024, 0) + ' Kb';	// String
			break;
		default:
			return format(size, 0) + ' bytes';	// String
			break;
	}
	return size;	// String
};

file.js.views.FileFormatter._formatNumber = function(/*Number*/ number, /*Integer*/ decimals, /*String*/ decPoint, /*String*/ thousandsSeparator) {
	// summary:
	//		Taken from http://phpjs.org/functions/number_format
	// author:
	//		http://phpjs.org
	// TODO: Use dojo.number.format
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousandsSeparator === 'undefined') ? ',' : thousandsSeparator,
			dec = (typeof decPoint === 'undefined') ? '.' : decPoint,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);		// String
};
