/**
 * NextCMS
 * 
 * @author		Firas Kassem <phiras@gmail.com>
 * @author		Amin Rajaee <rajaee@gmail.com>
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @see			http://phiras.wordpress.com/2007/04/08/password-strength-meter-a-jquery-plugin/
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage 	js
 * @since		1.0
 * @version		2011-11-01
 */

dojo.provide("core.js.views.PasswordStrengthMeter");

dojo.require("core.js.base.I18N");

dojo.declare("core.js.views.PasswordStrengthMeter", null, {
	// _id: String
	// 		The container's Id
	_id: null,

	// _username: String
	_username: null,
	
	// _i18n: Object
	_i18n: null,

	constructor: function(/*String*/ id) {
		this._id = id;
		
		core.js.base.I18N.requireLocalization("core/languages");
		this._i18n = core.js.base.I18N.getLocalization("core/languages");
	},

	setUsername: function(/*String*/ username) {
		// summary:
		// Sets the username. The password is bad if it is the same as the
		// username
		this._username = username;
	},

	getScore: function(/*String*/ password) {
		// summary:
		// 		Gets score of given password
		// description:
		// 		Below are some rules used to calculate password strength:
		// 		(note that there is no official rules)
		// 		- If the password matches the username then score = 0
		// 		- If the password is less than 4 characters then score = 0
		// 		score += password length * 4
		// 		score -= repeated characters in the password (1 char repetition)
		// 		score -= repeated characters in the password (2 char repetition)
		// 		score -= repeated characters in the password (3 char repetition)
		// 		score -= repeated characters in the password (4 char repetition)
		// 		- If the password has 3 numbers then score += 5
		// 		- If the password has 2 special characters then score += 5
		// 		- If the password has upper and lower character then score += 10
		// 		- If the password has numbers and characters then score += 15
		// 		- If the password has numbers and special characters then score += 15
		// 		- If the password has special characters and characters then score += 15
		// 		- If the password is only characters then score -= 10
		// 		- If the password is only numbers then score -= 10
		// 		- If score > 100 then score = 100
		//
		// 		The password strength is based on the score:
		// 		- If 0 < score <= 34 then the password is BAD
		// 		- If 34 < score <= 68 then the password is GOOD
		// 		- If 68 < score <= 100 then the password is STRONG
		var score = 0;

		// password's length < 4
		if (password.length < 4) {
			return 0;	// Integer
		}

		// password == username
		if (this._username && password.toLowerCase() == this._username.toLowerCase()) {
			return 0;	// Integer
		}

		// password length
		score += password.length * 4;
		score += (this._checkRepetition(1, password).length - password.length) * 1;
		score += (this._checkRepetition(2, password).length - password.length) * 1;
		score += (this._checkRepetition(3, password).length - password.length) * 1;
		score += (this._checkRepetition(4, password).length - password.length) * 1;

		// password has 3 numbers
		if (password.match(/(.*[0-9].*[0-9].*[0-9])/)) {
			score += 5;
		}

		// password has 2 symbols
		if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) {
			score += 5;
		}

		// password has Upper and Lower chars
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
			score += 10;
		}

		// password has number and chars
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) {
			score += 15;
		}
		
		// password has number and symbol
		if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/)) {
			score += 15;
		}

		// password has char and symbol
		if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/)) {
			score += 15;
		}

		// password is just a nubers or chars
		if (password.match(/^\w+$/) || password.match(/^\d+$/)) {
			score -= 10;
		}
		
		return (score > 100) ? 100 : score;		// Integer
	},
	
	checkPassword: function(/*String*/ password) {
		// summary:
		//		Checks the password strength, shows the result in a progress bar
		dojo.attr(this._id, {
			innerHTML: ""
		});
		
		// Don't show the status bar if the password is empty
		if (password == "") {
			return;
		}
		
		dojo.addClass(this._id, "appPasswordStrengthMeterContainer");
		var score = this.getScore(password);
		
		// The container showing the percent bar
		dojo.create("div", {
			className: "appPasswordStrengthMeterPercentBarContainer"
		}, this._id);
		
		// The percent bar
		dojo.create("div", {
			style: "width: " + ((score < 10) ? 10 : score) + "%",
			className: "appPasswordStrengthMeterPercentBar"
		}, this._id);
		
		var status = "";
		switch (true) {
			case (0 <= score && score <= 34):
				status = this._i18n.user._share.badPassword;
				break;
			case (34 < score && score <= 68):
				status = this._i18n.user._share.goodPassword;
				break;
			case (68 < score && score <= 100):
				status = this._i18n.user._share.strongPassword;
				break;
			default:
				break;
		}

		// Show the password strength (bad, good or strong)
		dojo.create("div", {
			innerHTML: status,
			className: "appPasswordStrengthMeterLabel"
		}, this._id);
	},

	_checkRepetition: function(/*Integer*/ pLen, /*String*/ str) {
		// example:
		// 		_checkRepetition(1, 'aaaaaaabcbc')	 = 'abcbc';
		//		_checkRepetition(2, 'aaaaaaabcbc')	 = 'aabc';
		//		_checkRepetition(2, 'aaaaaaabcdbcd') = 'aabcd';
		var res = "", repeated;
		for (var i = 0; i < str.length; i++) {
			repeated = true;
			for (var j = 0; j < pLen && (j + i + pLen) < str.length; j++) {
				repeated = repeated && (str.charAt(j + i) == str.charAt(j + i + pLen));
			}
			if (j < pLen) {
				repeated = false;
			}
			if (repeated) {
				i += pLen - 1;
				repeated = false;
			} else {
				res += str.charAt(i);
			}
		}
		return res;		// String
	}
});
