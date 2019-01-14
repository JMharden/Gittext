var myApp = angular.module("myApp", ["ionic", "app-controllers", "app-service"]);
myApp.config(["$stateProvider", "$urlRouterProvider", "$ionicConfigProvider", function($stateProvider, $urlRouterProvider, $ionicConfigProvider) {
	$ionicConfigProvider.platform.ios.tabs.style("standard");
	$ionicConfigProvider.platform.ios.tabs.position("bottom");
	$ionicConfigProvider.platform.ios.navBar.alignTitle("center");
	$ionicConfigProvider.platform.ios.views.transition("ios");
	$ionicConfigProvider.platform.android.tabs.style("standard");
	$ionicConfigProvider.platform.android.tabs.position("bottom");
	$ionicConfigProvider.platform.android.navBar.alignTitle("center");
	$ionicConfigProvider.platform.android.views.transition("none");
}]);
myApp.factory("focus", ["$timeout", "$window", function($timeout, $window) {
	return function(id) {
		$timeout(function() {
			var element = $window.document.getElementById(id);
			if (element) {
				element.focus()
			}
		})
	}
}]);
(function(m, ei, q, i, a, j, s) {
	m[i] = m[i] ||
	function() {
		(m[i].a = m[i].a || []).push(arguments)
	};
	j = ei.createElement(q), s = ei.getElementsByTagName(q)[0];
	j.async = true;
	j.charset = "UTF-8";
	j.src = "//static.meiqia.com/dist/meiqia.js";
	s.parentNode.insertBefore(j, s)
})(window, document, "script", "_MEIQIA");
if (typeof(_MEIQIA) == "undefined") {
	_MEIQIA = function() {}
}
_MEIQIA("entId", 43970);
_MEIQIA("withoutBtn");
myApp.run(function($rootScope, $location, trade) {
	if (navigator.userAgent.indexOf("UCBrowser") > -1) {
		document.getElementsByTagName("html")[0].style.fontSize = "5px"
	}(function() {
		if (typeof WeixinJSBridge == "object" && typeof WeixinJSBridge.invoke == "function") {
			handleFontSize()
		} else {
			if (document.addEventListener) {
				document.addEventListener("WeixinJSBridgeReady", handleFontSize, false)
			} else {
				if (document.attachEvent) {
					document.attachEvent("WeixinJSBridgeReady", handleFontSize);
					document.attachEvent("onWeixinJSBridgeReady", handleFontSize)
				}
			}
		}
		function handleFontSize() {
			WeixinJSBridge.invoke("setFontSizeCallback", {
				fontSize: 0
			});
			WeixinJSBridge.on("menu:setfont", function() {
				WeixinJSBridge.invoke("setFontSizeCallback", {
					fontSize: 0
				})
			})
		}
	})()
});
var appService = angular.module("app-service", []);
var controllers = angular.module("app-controllers", []);
Array.prototype.clear = function() {
	if (this.length > 0) {
		this.length = 0
	}
	return this
};
Date.prototype.format = function(format) {
	var o = {
		"M+": this.getMonth() + 1,
		"d+": this.getDate(),
		"h+": this.getHours(),
		"m+": this.getMinutes(),
		"s+": this.getSeconds(),
		"q+": Math.floor((this.getMonth() + 3) / 3),
		S: this.getMilliseconds()
	};
	if (/(y+)/.test(format)) {
		format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length))
	}
	for (var k in o) {
		if (new RegExp("(" + k + ")").test(format)) {
			format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length))
		}
	}
	return format
};

function StringBuffer() {
	this.__strings__ = []
}
StringBuffer.prototype.append = function(str) {
	this.__strings__.push(str);
	return this
};
StringBuffer.prototype.toString = function() {
	return this.__strings__.join("")
};
var JSUtils = {
	system: {
		addHandler: function(element, type, handler) {
			if (element.addEventListener) {
				element.addEventListener(type, handler, false)
			} else {
				if (element.attachEvent) {
					element.attachEvent("on" + type, handler)
				} else {
					element["on" + type] = handler
				}
			}
		},
		setTimeOut: function(callback, timeout, param) {
			var args = Array.prototype.slice.call(arguments, 2);
			var _cb = function() {
					callback.apply(null, args)
				};
			return window.setTimeout(_cb, timeout)
		},
		setInterval: function(callback, timeout, param) {
			var args = Array.prototype.slice.call(arguments, 2);
			var _cb = function() {
					callback.apply(null, args)
				};
			return window.setInterval(_cb, timeout)
		},
		getPageParamVal: function(name) {
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
			var r = window.location.search.substr(1).match(reg);
			if (r != null) {
				return r[2]
			} else {
				return null
			}
		}
	},
	number: {
		getSepNum: function(inputValue, minValue, maxValue) {
			if (inputValue < minValue) {
				inputValue = minValue
			} else {
				if (inputValue > maxValue) {
					inputValue = maxValue
				}
			}
			return inputValue
		},
		getMin: function(val1, val2) {
			if (val1 > val2) {
				return val2
			} else {
				return val1
			}
		},
		getMax: function(val1, val2) {
			if (val1 > val2) {
				return val1
			} else {
				return val2
			}
		},
		getRandomInt: function(min, max) {
			if (min > max || max < 0) {
				throw "getRandomInt  指定参数错误"
			}
			var tmp = 0;
			if (min < 0) {
				tmp = min;
				max = max - min;
				min = 0
			}
			return JSUtils.number.chnInt(Math.random() * (max - min + 1) + min) + tmp
		},
		getFormatNum: function(num) {
			if (isNaN(JSUtils.number.chnInt(num))) {
				return num
			}
			if (JSUtils.number.chnInt(num) == parseFloat(num)) {
				return JSUtils.number.chnInt(num)
			} else {
				return parseFloat(num)
			}
		},
		formatNumeric: function(num, numericLen) {
			var val = Math.pow(10, numericLen);
			num = JSUtils.number.chnInt(Math.round(num * val)) / val;
			var str = num.toString();
			var str1, num1, num2 = str.indexOf(".");
			if (num2 == -1) {
				num1 = numericLen;
				if (numericLen != 0) {
					str += "."
				}
			} else {
				str1 = str.substr(num2 + 1, str.length);
				num1 = numericLen - str1.length
			}
			var str2 = "";
			for (var i = 0; i < num1; i++) {
				str2 += "0"
			}
			return str + str2
		},
		IN: function(val, array) {
			for (var i = 0; i < array.length; i++) {
				if (val == array[i]) {
					return true
				}
			}
			return false
		},
		chnInt: function(req) {
			return parseInt(req, 10)
		},
		oneFloat: function(param) {
			var param = param + "";
			var strarr = param.split(".");
			if (strarr.length <= 1) {
				strarr.push("0")
			} else {
				if (strarr[1].length > 1) {
					strarr[1] = (strarr[1] + "").substr(0, 1)
				}
			}
			return strarr
		},
		fiveFloat: function(param) {
			var param = param + "";
			var strarr = param.split(".");
			if (strarr.length <= 1) {
				strarr.push("00000")
			} else {
				if (strarr[1].length >= 5) {
					strarr[1] = (strarr[1] + "").substr(0, 5)
				} else {
					if (strarr[1].length < 5 || strarr[1].length > 0) {
						if (strarr[1].length == 1) {
							strarr[1] = strarr[1] + "0000"
						} else {
							if (strarr[1].length == 2) {
								strarr[1] = strarr[1] + "000"
							} else {
								if (strarr[1].length == 3) {
									strarr[1] = strarr[1] + "00"
								} else {
									if (strarr[1].length == 4) {
										strarr[1] = strarr[1] + "0"
									}
								}
							}
						}
					}
				}
			}
			return strarr
		},
		moneySplitIntAndFloat: function(money) {
			if (!money) {
				return ["0", "00"]
			} else {
				var str = money + "";
				var strarr = str.split(".");
				if (strarr.length <= 1) {
					strarr.push("00")
				} else {
					if (strarr[1].length <= 1) {
						strarr[1] += "0"
					} else {
						if (strarr[1].length > 1) {
							strarr[1] = strarr[1].substr(0, 2)
						}
					}
				}
				return strarr
			}
		}
	},
	object: {
		hasProperty: function(obj) {
			for (var i in obj) {
				return true
			}
			return false
		},
		hasSepProperty: function(obj, propName) {
			return obj.hasOwnProperty(propName)
		},
		deleteSepProperty: function(obj, propName) {
			delete obj[propName]
		},
		exist: function(obj) {
			try {
				if (obj == null) {
					return false
				}
				if (obj == undefined) {
					return false
				}
				return typeof(obj) != "undefined"
			} catch (e) {
				return false
			}
		},
		strToJsonObj: function(jsonStr) {
			if (JSUtils.string.isAvailably(jsonStr)) {
				return eval("(" + jsonStr + ")")
			}
			return eval("(null)")
		},
		toJsonStr: function(obj) {
			return JSON.stringify(obj)
		},
		jsonClone: function(jsonObj) {
			var buf;
			if (jsonObj instanceof Array) {
				buf = [];
				var i = jsonObj.length;
				while (i--) {
					buf[i] = JSUtils.object.jsonClone(jsonObj[i])
				}
				return buf
			} else {
				if (jsonObj instanceof Object) {
					buf = {};
					for (var k in jsonObj) {
						buf[k] = JSUtils.object.jsonClone(jsonObj[k])
					}
					return buf
				} else {
					return jsonObj
				}
			}
		},
		arraySort: function(array, order, columnName) {
			array.sort(function(obj1, obj2) {
				var val1, val2, tmpVal;
				var ret;
				val1 = obj1[columnName];
				val2 = obj2[columnName];
				if (order == 0) {
					tmpVal = val2;
					val2 = val1;
					val1 = tmpVal
				}
				if (val1 < val2) {
					ret = -1
				} else {
					if (val1 > val2) {
						ret = 1
					} else {
						ret = 0
					}
				}
				return ret
			})
		}
	},
	string: {
		isAvailably: function(str) {
			if (str == null) {
				return false
			}
			if (typeof(str) == "undefined") {
				return false
			}
			if (str == "") {
				return false
			}
			if (str == "null" || str == "undefined") {
				return false
			}
			return true
		},
		getAvailablyString: function(str1, str2) {
			return JSUtils.string.isAvailably(str1) ? str1 : str2
		},
		getFormatString: function(str, replaceWords) {
			var ret = str;
			if (replaceWords == null) {
				return str
			}
			for (var i = 0; i < replaceWords.length; i++) {
				var re = eval("/\\{" + i + "\\}/ig");
				ret = ret.replace(re, replaceWords[i])
			}
			return ret
		},
		replaceWord: function(source, rep1, rep2) {
			return source.replace(new RegExp(rep1, "gm"), rep2)
		},
		getStressHtml: function(str, replaceWords, stressWordCss) {
			if (replaceWords == null) {
				return str
			}
			for (var i = 0; i < replaceWords.length; i++) {
				var tmpRepObj = replaceWords[i];
				if (typeof(tmpRepObj) == "object") {
					replaceWords[i] = JSUtils.string.getReplaceSpan(tmpRepObj.css, tmpRepObj.word)
				} else {
					replaceWords[i] = JSUtils.string.getReplaceSpan(stressWordCss, tmpRepObj)
				}
			}
			return JSUtils.string.getFormatString(str, replaceWords)
		},
		getNormalHtml: function(str, replaceWords, stressWordCss) {
			if (replaceWords == null) {
				return str
			}
			for (var i = 0; i < replaceWords.length; i++) {
				var tmpRepObj = replaceWords[i];
				if (typeof(tmpRepObj) == "object") {
					replaceWords[i] = "<span class='noempty " + tmpRepObj.css + "'>" + tmpRepObj.word + "</span>"
				} else {
					replaceWords[i] = "<span class='noempty " + stressWordCss + "'>" + tmpRepObj + "</span>"
				}
			}
			return JSUtils.string.getFormatString(str, replaceWords)
		},
		getReplaceSpan: function(css, word) {
			return "<span class='noempty " + css + "'> " + word + " </span>"
		},
		getRandomUrlParams: function() {
			return "random=" + (new Date().getTime() + Math.random())
		},
		startWith: function(sourceStr, startStr) {
			var d = startStr.length;
			return (d >= 0 && sourceStr.indexOf(startStr) == 0)
		},
		endWith: function(sourceStr, endStr) {
			var d = sourceStr.length - endStr.length;
			return (d >= 0 && sourceStr.lastIndexOf(endStr) == d)
		},
		contains: function(sourceStr, containStr) {
			return (sourceStr.indexOf(containStr) > 0)
		},
		phoneToShow: function(phoneNum) {
			if (!JSUtils.form.check.isPhone(phoneNum)) {
				return phoneNum
			}
			return phoneNum.substring(0, 3) + "******" + phoneNum.substring(phoneNum.length - 2, phoneNum.length)
		}
	},
	form: {
		check: {
			isEmpty: function(objVal) {
				return !JSUtils.string.isAvailably(objVal)
			},
			isMail: function(objVal) {
				var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
				return pattern.test(objVal)
			},
			isDigit: function(objVal) {
				var reg = new RegExp("^[0-9]*$");
				return reg.test(objVal)
			},
			isPhone: function(str) {
				var re = /^(13[0-9]{9})|(14[0-9]{9})|(15[0-9]{9})|(18[0-9]{9})$/;
				return re.test(str)
			}
		}
	},
	data: {},
	localSave: {
		get: function(key) {
			var localStr = window.localStorage.getItem(key);
			return JSUtils.object.strToJsonObj(localStr)
		},
		set: function(key, val) {
			var localStr = JSUtils.object.toJsonStr(val);
			window.localStorage.setItem(key, localStr)
		},
		remove: function(key) {
			window.localStorage.removeItem(key)
		}
	},
	date: {
		timeStrTODate: function(timeStr) {
			if (!JSUtils.string.isAvailably(timeStr)) {
				return null
			}
			if (timeStr.length != 14) {
				return null
			}
			var str = timeStr.substr(0, 4) + "/" + timeStr.substr(4, 2) + "/" + timeStr.substr(6, 2) + " " + timeStr.substr(8, 2) + ":" + timeStr.substr(10, 2) + ":" + timeStr.substr(12, 2);
			return new Date(str)
		},
		timeStrToTime: function(timeStr) {
			if (!JSUtils.string.isAvailably(timeStr)) {
				return null
			}
			if (timeStr.length != 14) {
				return null
			}
			return timeStr.substr(4, 2) + "." + timeStr.substr(6, 2) + " - " + timeStr.substr(8, 2) + ":" + timeStr.substr(10, 2) + ":" + timeStr.substr(12, 2)
		}
	},
	curApp: {
		constants: {
			tradeInfoFormat: {
				btc_cny: 2,
				ltc_cny: 2,
				"USDJPY.FX": 5,
				"EURJPY.FX": 5,
				"IF00C1.CFE": 2,
				"HSI.HI": 2,
				"IXIC.GI": 2,
				"GDAXI.GI": 2,
				"GC16Z.CMX": 2,
				"USDCAD.FX": 5,
				"GC00Y.CMX": 2
			}
		},
		sessionObj: {
			put: function(key, value) {
				if (!JSUtils.string.isAvailably(value)) {
					return
				}
				var sessionObj = JSUtils.curApp.sessionObj.__getObj();
				if (!sessionObj) {
					sessionObj = {}
				}
				sessionObj[key] = value;
				JSUtils.curApp.sessionObj.__saveObj(sessionObj)
			},
			get: function(key) {
				var sessionObj = JSUtils.curApp.sessionObj.__getObj();
				if (sessionObj) {
					return sessionObj[key]
				}
				return ""
			},
			__getObj: function() {
				var sessionObj = JSUtils.localSave.get("sessionObj");
				if (!sessionObj) {
					sessionObj = {}
				}
				return sessionObj
			},
			__saveObj: function(sobj) {
				JSUtils.localSave.set("sessionObj", sobj)
			}
		}
	}
};

function md5(str) {
	return hex_md5(str);

	function hex_md5(a) {
		if (a == "") {
			return a
		}
		return rstr2hex(rstr_md5(str2rstr_utf8(a)))
	}
	function hex_hmac_md5(a, b) {
		return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a), str2rstr_utf8(b)))
	}
	function md5_vm_test() {
		return hex_md5("abc").toLowerCase() == "900150983cd24fb0d6963f7d28e17f72"
	}
	function rstr_md5(a) {
		return binl2rstr(binl_md5(rstr2binl(a), a.length * 8))
	}
	function rstr_hmac_md5(c, f) {
		var e = rstr2binl(c);
		if (e.length > 16) {
			e = binl_md5(e, c.length * 8)
		}
		var a = Array(16),
			d = Array(16);
		for (var b = 0; b < 16; b++) {
			a[b] = e[b] ^ 909522486;
			d[b] = e[b] ^ 1549556828
		}
		var g = binl_md5(a.concat(rstr2binl(f)), 512 + f.length * 8);
		return binl2rstr(binl_md5(d.concat(g), 512 + 128))
	}
	function rstr2hex(c) {
		try {
			hexcase
		} catch (g) {
			hexcase = 0
		}
		var f = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		var b = "";
		var a;
		for (var d = 0; d < c.length; d++) {
			a = c.charCodeAt(d);
			b += f.charAt((a >>> 4) & 15) + f.charAt(a & 15)
		}
		return b
	}
	function str2rstr_utf8(c) {
		var b = "";
		var d = -1;
		var a, e;
		while (++d < c.length) {
			a = c.charCodeAt(d);
			e = d + 1 < c.length ? c.charCodeAt(d + 1) : 0;
			if (55296 <= a && a <= 56319 && 56320 <= e && e <= 57343) {
				a = 65536 + ((a & 1023) << 10) + (e & 1023);
				d++
			}
			if (a <= 127) {
				b += String.fromCharCode(a)
			} else {
				if (a <= 2047) {
					b += String.fromCharCode(192 | ((a >>> 6) & 31), 128 | (a & 63))
				} else {
					if (a <= 65535) {
						b += String.fromCharCode(224 | ((a >>> 12) & 15), 128 | ((a >>> 6) & 63), 128 | (a & 63))
					} else {
						if (a <= 2097151) {
							b += String.fromCharCode(240 | ((a >>> 18) & 7), 128 | ((a >>> 12) & 63), 128 | ((a >>> 6) & 63), 128 | (a & 63))
						}
					}
				}
			}
		}
		return b
	}
	function rstr2binl(b) {
		var a = Array(b.length >> 2);
		for (var c = 0; c < a.length; c++) {
			a[c] = 0
		}
		for (var c = 0; c < b.length * 8; c += 8) {
			a[c >> 5] |= (b.charCodeAt(c / 8) & 255) << (c % 32)
		}
		return a
	}
	function binl2rstr(b) {
		var a = "";
		for (var c = 0; c < b.length * 32; c += 8) {
			a += String.fromCharCode((b[c >> 5] >>> (c % 32)) & 255)
		}
		return a
	}
	function binl_md5(p, k) {
		p[k >> 5] |= 128 << ((k) % 32);
		p[(((k + 64) >>> 9) << 4) + 14] = k;
		var o = 1732584193;
		var n = -271733879;
		var m = -1732584194;
		var l = 271733878;
		for (var g = 0; g < p.length; g += 16) {
			var j = o;
			var h = n;
			var f = m;
			var e = l;
			o = md5_ff(o, n, m, l, p[g + 0], 7, -680876936);
			l = md5_ff(l, o, n, m, p[g + 1], 12, -389564586);
			m = md5_ff(m, l, o, n, p[g + 2], 17, 606105819);
			n = md5_ff(n, m, l, o, p[g + 3], 22, -1044525330);
			o = md5_ff(o, n, m, l, p[g + 4], 7, -176418897);
			l = md5_ff(l, o, n, m, p[g + 5], 12, 1200080426);
			m = md5_ff(m, l, o, n, p[g + 6], 17, -1473231341);
			n = md5_ff(n, m, l, o, p[g + 7], 22, -45705983);
			o = md5_ff(o, n, m, l, p[g + 8], 7, 1770035416);
			l = md5_ff(l, o, n, m, p[g + 9], 12, -1958414417);
			m = md5_ff(m, l, o, n, p[g + 10], 17, -42063);
			n = md5_ff(n, m, l, o, p[g + 11], 22, -1990404162);
			o = md5_ff(o, n, m, l, p[g + 12], 7, 1804603682);
			l = md5_ff(l, o, n, m, p[g + 13], 12, -40341101);
			m = md5_ff(m, l, o, n, p[g + 14], 17, -1502002290);
			n = md5_ff(n, m, l, o, p[g + 15], 22, 1236535329);
			o = md5_gg(o, n, m, l, p[g + 1], 5, -165796510);
			l = md5_gg(l, o, n, m, p[g + 6], 9, -1069501632);
			m = md5_gg(m, l, o, n, p[g + 11], 14, 643717713);
			n = md5_gg(n, m, l, o, p[g + 0], 20, -373897302);
			o = md5_gg(o, n, m, l, p[g + 5], 5, -701558691);
			l = md5_gg(l, o, n, m, p[g + 10], 9, 38016083);
			m = md5_gg(m, l, o, n, p[g + 15], 14, -660478335);
			n = md5_gg(n, m, l, o, p[g + 4], 20, -405537848);
			o = md5_gg(o, n, m, l, p[g + 9], 5, 568446438);
			l = md5_gg(l, o, n, m, p[g + 14], 9, -1019803690);
			m = md5_gg(m, l, o, n, p[g + 3], 14, -187363961);
			n = md5_gg(n, m, l, o, p[g + 8], 20, 1163531501);
			o = md5_gg(o, n, m, l, p[g + 13], 5, -1444681467);
			l = md5_gg(l, o, n, m, p[g + 2], 9, -51403784);
			m = md5_gg(m, l, o, n, p[g + 7], 14, 1735328473);
			n = md5_gg(n, m, l, o, p[g + 12], 20, -1926607734);
			o = md5_hh(o, n, m, l, p[g + 5], 4, -378558);
			l = md5_hh(l, o, n, m, p[g + 8], 11, -2022574463);
			m = md5_hh(m, l, o, n, p[g + 11], 16, 1839030562);
			n = md5_hh(n, m, l, o, p[g + 14], 23, -35309556);
			o = md5_hh(o, n, m, l, p[g + 1], 4, -1530992060);
			l = md5_hh(l, o, n, m, p[g + 4], 11, 1272893353);
			m = md5_hh(m, l, o, n, p[g + 7], 16, -155497632);
			n = md5_hh(n, m, l, o, p[g + 10], 23, -1094730640);
			o = md5_hh(o, n, m, l, p[g + 13], 4, 681279174);
			l = md5_hh(l, o, n, m, p[g + 0], 11, -358537222);
			m = md5_hh(m, l, o, n, p[g + 3], 16, -722521979);
			n = md5_hh(n, m, l, o, p[g + 6], 23, 76029189);
			o = md5_hh(o, n, m, l, p[g + 9], 4, -640364487);
			l = md5_hh(l, o, n, m, p[g + 12], 11, -421815835);
			m = md5_hh(m, l, o, n, p[g + 15], 16, 530742520);
			n = md5_hh(n, m, l, o, p[g + 2], 23, -995338651);
			o = md5_ii(o, n, m, l, p[g + 0], 6, -198630844);
			l = md5_ii(l, o, n, m, p[g + 7], 10, 1126891415);
			m = md5_ii(m, l, o, n, p[g + 14], 15, -1416354905);
			n = md5_ii(n, m, l, o, p[g + 5], 21, -57434055);
			o = md5_ii(o, n, m, l, p[g + 12], 6, 1700485571);
			l = md5_ii(l, o, n, m, p[g + 3], 10, -1894986606);
			m = md5_ii(m, l, o, n, p[g + 10], 15, -1051523);
			n = md5_ii(n, m, l, o, p[g + 1], 21, -2054922799);
			o = md5_ii(o, n, m, l, p[g + 8], 6, 1873313359);
			l = md5_ii(l, o, n, m, p[g + 15], 10, -30611744);
			m = md5_ii(m, l, o, n, p[g + 6], 15, -1560198380);
			n = md5_ii(n, m, l, o, p[g + 13], 21, 1309151649);
			o = md5_ii(o, n, m, l, p[g + 4], 6, -145523070);
			l = md5_ii(l, o, n, m, p[g + 11], 10, -1120210379);
			m = md5_ii(m, l, o, n, p[g + 2], 15, 718787259);
			n = md5_ii(n, m, l, o, p[g + 9], 21, -343485551);
			o = safe_add(o, j);
			n = safe_add(n, h);
			m = safe_add(m, f);
			l = safe_add(l, e)
		}
		return Array(o, n, m, l)
	}
	function md5_cmn(h, e, d, c, g, f) {
		return safe_add(bit_rol(safe_add(safe_add(e, h), safe_add(c, f)), g), d)
	}
	function md5_ff(g, f, k, j, e, i, h) {
		return md5_cmn((f & k) | ((~f) & j), g, f, e, i, h)
	}
	function md5_gg(g, f, k, j, e, i, h) {
		return md5_cmn((f & j) | (k & (~j)), g, f, e, i, h)
	}
	function md5_hh(g, f, k, j, e, i, h) {
		return md5_cmn(f ^ k ^ j, g, f, e, i, h)
	}
	function md5_ii(g, f, k, j, e, i, h) {
		return md5_cmn(k ^ (f | (~j)), g, f, e, i, h)
	}
	function safe_add(a, d) {
		var c = (a & 65535) + (d & 65535);
		var b = (a >> 16) + (d >> 16) + (c >> 16);
		return (b << 16) | (c & 65535)
	}
	function bit_rol(a, b) {
		return (a << b) | (a >>> (32 - b))
	}
}
var controllers = angular.module("app-controllers", []);
controllers.controller("meContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.headimgurl = "";
	var defaultMoneySplit = JSUtils.number.moneySplitIntAndFloat(null);
	$scope.rmbmoneyInt = defaultMoneySplit[0];
	$scope.rmbmoneyFloat = defaultMoneySplit[1];
	$scope.userPhone = "";
	$scope.userName = "";
	$scope.spot_flag = false;

	function setup(data) {
		var userInfo = data.userinfo;
		if (userInfo) {
			$scope.headimgurl = userInfo.headimgurl;
			$scope.headimgurl = userInfo.headimgurl;
			if (userInfo.username) {
				$scope.userName = userInfo.username
			} else {
				$scope.userName = "未设定"
			}
		}
		var accountList = data.account;
		if (accountList) {
			for (var i = 0; i < accountList.length; i++) {
				var account = accountList[i];
				var splitMoney = JSUtils.number.moneySplitIntAndFloat(account.abalance);
				if (account.atype == "R") {
					$scope.rmbmoneyInt = splitMoney[0];
					$scope.rmbmoneyFloat = splitMoney[1]
				} else {
					if (account.atype == "V") {} else {
						if (account.atype == "C") {}
					}
				}
			}
		}
	}
	$scope.contact = function() {
		_MEIQIA("showPanel")
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("我的数字账户");
		trade.userInfoQuery(function(data, status, err, srvJson) {
			if (err.errcode == "2000") {
				setup(srvJson)
			} else {}
		}, function(data, state) {});
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("meResultContrl", ["$scope", "$ionicTabsDelegate", "trade", function($scope, $ionicTabsDelegate, trade) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.feedbacks = [];
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("处理结果");
		trade.feedBackQuery(function(data, status, err, srvJson) {
			if (data.error["errcode"] == "2000") {
				$scope.feedbacks = [];
				if (data.data.feedbacks) {
					for (var i = 0; i < data.data.feedbacks.length; i++) {
						var tmpFB = data.data.feedbacks[i];
						tmpFB.showTime = "";
						if (JSUtils.string.isAvailably(tmpFB.time)) {
							var tmpD = JSUtils.date.timeStrTODate(tmpFB.time);
							tmpFB.showTime = tmpD.format("yyyy-MM-dd hh:mm:ss")
						}
						$scope.feedbacks.push(tmpFB)
					}
				}
			}
		}, function(data, status) {});
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("me_NameContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.nameset = function() {
		var username = document.getElementById("myName").value;
		trade.serNameuSet(username, function(data, state) {
			if (data.error["errcode"] == "2000") {
				trade.alert("保存成功");
				$location.path("tabs/me")
			} else {}
		}, null)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("姓名");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("mechildContrl", ["$scope", "$ionicTabsDelegate", "$location", "trade", function($scope, $ionicTabsDelegate, $location, trade) {
	$scope.selectIndex = 0;
	$scope.channels = ["专属链接", "佣金统计", "数据明细"];
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.channelChange = function(index) {
		$scope.selectIndex = index;
		if (index == 0) {
			document.getElementsByClassName("me_only")[0].style.display = "block";
			document.getElementsByClassName("statistical")[0].style.display = "none";
			document.getElementsByClassName("me_agent")[0].style.display = "none"
		} else {
			if (index == 1) {
				document.getElementsByClassName("me_only")[0].style.display = "none";
				document.getElementsByClassName("statistical")[0].style.display = "block";
				document.getElementsByClassName("me_agent")[0].style.display = "none"
			} else {
				if (index == 2) {
					document.getElementsByClassName("statistical")[0].style.display = "none";
					document.getElementsByClassName("me_only")[0].style.display = "none";
					document.getElementsByClassName("me_agent")[0].style.display = "block"
				}
			}
		}
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		if ($location.path() == "/tabs/commonProblem_me" || $location.path() == "/tabs/moneyPackQuestion_money") {
			trade.update_wx_title("提交问题")
		}
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("moneyDetailMeContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.selectIndex = 0;
	$scope.selectIndex_range = 3;
	$scope.dtlTrades = [];
	$scope.dtlDCInfos = [];
	$scope.dayRange = [];
	$scope.weekRange = [];
	$scope.monthRange = [];

	function moneyDetail() {
		trade.dtlDCInfosQuery(function(data, state, error, srvJson) {
			$scope.dtlDCInfos = [];
			if (srvJson.dcinfos) {
				for (var i = 0; i < srvJson.dcinfos.length; i++) {
					var tmpInfo = srvJson.dcinfos[i];
					tmpInfo.showName = "";
					if (tmpInfo.dctype == "pay") {
						tmpInfo.showName = "账户充值"
					} else {
						if (tmpInfo.dctype == "cashback") {
							tmpInfo.showName = "账户提现"
						} else {
							if (tmpInfo.dctype == "charge") {
								tmpInfo.showName = "手续费"
							} else {
								if (tmpInfo.dctype == "coupon") {
									tmpInfo.showName = "领取红包"
								} else {
									if (tmpInfo.dctype == "order") {
										tmpInfo.showName = "下单"
									} else {
										if (tmpInfo.dctype == "trade") {
											tmpInfo.showName = "交割"
										}
									}
								}
							}
						}
					}
					tmpInfo.showTime = "";
					if (JSUtils.string.isAvailably(tmpInfo.dctime)) {
						var tmpD = JSUtils.date.timeStrTODate(tmpInfo.dctime);
						tmpInfo.showTime = tmpD.format("yyyy-MM-dd hh:mm:ss")
					}
					tmpInfo.showMoney = "";
					if (tmpInfo.dcflag == "d") {
						tmpInfo.showMoney = "+ " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
					} else {
						if (tmpInfo.dcflag == "c") {
							tmpInfo.showMoney = "- " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
						}
					}
					$scope.dtlDCInfos.push(tmpInfo)
				}
			}
		}, null)
	}
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("明细");
		moneyDetail();
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("qitaQuestionContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.otherQuestionClick = function() {
		var telnum = document.getElementById("phone").value;
		var comment = document.getElementById("comment").value;
		if (!JSUtils.string.isAvailably(telnum)) {
			trade.alert("请输入手机");
			return
		}
		if (!JSUtils.string.isAvailably(comment)) {
			trade.alert("请输入您遇到的问题");
			return
		}
		var backFeed = {};
		backFeed.telnum = telnum;
		backFeed.comment = comment;
		trade.backFeed(backFeed, function(data, status) {
			if (data.error["errcode"] == "2000") {
				trade.alert("您反馈的信息我们会尽快处理！");
				$location.path("tabs/me")
			} else {}
			document.getElementById("phone").value = "";
			document.getElementById("comment").value = ""
		}, null)
	};
	trade.feedBackQuery(function(data, state) {
		trade.update_wx_title("其他问题")
	});
	$scope.$on("$ionicView.afterEnter", function() {
		$ionicTabsDelegate.showBar(false)
	})
}]);
controllers.controller("rulepageContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.username = "";
	$scope.ruleJump = function(val) {
		var path = $location.path().split("tabs/")[1].split("_")[1];
		var url = val + "_" + path;
		$location.path(url)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("常见问题");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.afterEnter", function() {
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		if ($location.path() == "/tabs/trade" || $location.path() == "/tabs/moneyPack" || $location.path() == "/tabs/discover" || $location.path() == "/tabs/me") {
			$ionicTabsDelegate.showBar(true)
		}
	})
}]);
controllers.controller("rulepagechildContrl", ["$scope", "$ionicTabsDelegate", "$location", "trade", function($scope, $ionicTabsDelegate, $location, trade) {
	$scope.$on("$ionicView.afterEnter", function() {
		$ionicTabsDelegate.showBar(false)
	})
}]);
controllers.controller("tixianContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.tixianSubmitClick = function() {
		var feedback = {};
		var telnum = document.getElementById("phone").value;
		var amount = document.getElementById("amount").value;
		var comment = document.getElementById("comment").value;
		if (!JSUtils.string.isAvailably(telnum)) {
			trade.alert("请输入手机");
			return
		}
		if (!JSUtils.string.isAvailably(amount)) {
			trade.alert("请输入正确提现金额");
			return
		}
		if (!JSUtils.string.isAvailably(comment)) {
			trade.alert("请输入备注");
			return
		}
		amount = parseFloat(amount);
		if (amount <= 0) {
			trade.alert("请输入正确提现金额");
			return
		}
		feedback.telnum = telnum;
		feedback.amount = amount;
		feedback.comment = comment;
		trade.backFeed(feedback, function(data, status) {
			if (data.error["errcode"] == "2000") {
				trade.alert("您反馈的信息我们会尽快处理！");
				$location.path("tabs/me")
			} else {}
			document.getElementById("phone").value = "";
			document.getElementById("amount").value = "";
			document.getElementById("comment").value = ""
		}, function(error, status) {})
	};
	$scope.$on("$ionicView.afterEnter", function() {
		trade.update_wx_title("提现");
		$ionicTabsDelegate.showBar(false)
	})
}]);
controllers.controller("tradeDetailMeContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.selectIndex = 0;
	$scope.selectIndex_range = 3;
	$scope.dtlTrades = [];
	$scope.dtlDCInfos = [];
	$scope.dayRange = [];
	$scope.weekRange = [];
	$scope.monthRange = [];

	function tradeDetail() {
		trade.dtlTradesQuery(function(data, state) {
			var newarr = data.data.trades;
			var i;
			var targetinfosList = localStorage.getItem("targetinfos");
			var targetInfoMap = {};
			if (targetinfosList) {
				targetinfosList = JSUtils.object.strToJsonObj(targetinfosList);
				for (i = 0; i < targetinfosList.length; i++) {
					var tmpInfo = targetinfosList[i];
					targetInfoMap[tmpInfo.id] = tmpInfo
				}
			}
			$scope.dtlTrades = [];
			var changenameid = JSUtils.object.strToJsonObj(localStorage.getItem("targetinfos"));
			for (i = 0; i < newarr.length; i++) {
				var tmpnewarr = newarr[i];
				var temp = {};
				temp.billid = tmpnewarr.billid;
				temp.targetid = tmpnewarr.targetid;
				temp.interval = tmpnewarr.interval;
				temp.ordertime = JSUtils.date.timeStrToTime(tmpnewarr.ordertime);
				temp.tradetimeexpect = JSUtils.date.timeStrToTime(tmpnewarr.tradetimeexpect);
				temp.tradeprice = tmpnewarr.tradeprice;
				if (tmpnewarr.orderdir == "1") {
					temp.orderdir = "看跌"
				} else {
					if (tmpnewarr.orderdir == "2") {
						temp.orderdir = "看涨"
					} else {
						if (tmpnewarr.orderdir == "0") {
							temp.orderdir = "持平"
						}
					}
				}
				temp.one = false;
				temp.two = false;
				temp.three = false;
				temp.four = false;
				if (tmpnewarr.tprofitflag == 1) {
					temp.one = true
				} else {
					if (tmpnewarr.tprofitflag == 2) {
						temp.two = true
					} else {
						if (tmpnewarr.tprofitflag == 0) {
							temp.three = true
						} else {
							if (tmpnewarr.tprofitflag == 3) {
								temp.four = true
							}
						}
					}
				}
				temp.orderprice = JSUtils.number.formatNumeric(tmpnewarr.orderprice, JSUtils.curApp.constants.tradeInfoFormat[tmpnewarr.targetid]);
				temp.charge = JSUtils.number.formatNumeric(tmpnewarr.charge, 2);
				temp.ordernum = tmpnewarr.ordernum;
				temp.stakesum = JSUtils.number.formatNumeric(tmpnewarr.stakesum, 2);
				temp.profitrate = tmpnewarr.profitrate * 100;
				if (tmpnewarr.traderet == "1") {
					temp.traderet = "交割成功"
				} else {
					if (tmpnewarr.traderet == "2") {
						temp.traderet = "交割回滚"
					} else {
						if (tmpnewarr.traderet == "0") {
							temp.traderet = "未交割"
						}
					}
				}
				temp.tradeprice = JSUtils.number.formatNumeric(tmpnewarr.tradeprice, JSUtils.curApp.constants.tradeInfoFormat[tmpnewarr.targetid]);
				if (tmpnewarr.tprofitflag == "0") {
					temp.tprofitflag = "未交割"
				} else {
					if (tmpnewarr.tprofitflag == "1") {
						temp.tprofitflag = "盈利"
					} else {
						if (tmpnewarr.tprofitflag == "2") {
							temp.tprofitflag = "亏损"
						} else {
							if (tmpnewarr.tprofitflag == "3") {
								temp.tprofitflag = "打平"
							} else {
								if (tmpnewarr.tprofitflag == "4") {
									temp.tprofitflag = "异常"
								}
							}
						}
					}
				}
				temp.tradeprofit = JSUtils.number.formatNumeric(tmpnewarr.tradeprofit, 2);
				if (tmpnewarr.traderet == "0") {
					temp.traderet = "未查看"
				} else {
					if (tmpnewarr.traderet == "1") {
						temp.traderet = "已查看"
					}
				}
				if (tmpnewarr.type == "R") {
					temp.type = "实盘"
				} else {
					if (tmpnewarr.type == "V") {
						temp.type = "模拟"
					}
				}
				for (var j = 0; j < changenameid.length; j++) {
					if (changenameid[j]["targetid"] == tmpnewarr.targetid) {
						temp.showName = changenameid[j]["name"]
					}
				}
				if (JSUtils.object.hasSepProperty(targetInfoMap, tmpnewarr.targetid)) {
					temp.showName = targetInfoMap[tmpnewarr.targetid]["name"]
				}
				$scope.dtlTrades.push(temp)
			}
		}, null)
	}
	function moneyDetail() {
		trade.dtlDCInfosQuery(function(data, state, error, srvJson) {
			$scope.dtlDCInfos = [];
			if (srvJson.dcinfos) {
				for (var i = 0; i < srvJson.dcinfos.length; i++) {
					var tmpInfo = srvJson.dcinfos[i];
					tmpInfo.showName = "";
					if (tmpInfo.dctype == "pay") {
						tmpInfo.showName = "账户充值"
					} else {
						if (tmpInfo.dctype == "cashback") {
							tmpInfo.showName = "账户提现"
						} else {
							if (tmpInfo.dctype == "charge") {
								tmpInfo.showName = "手续费"
							} else {
								if (tmpInfo.dctype == "coupon") {
									tmpInfo.showName = "领取红包"
								} else {
									if (tmpInfo.dctype == "order") {
										tmpInfo.showName = "下单"
									} else {
										if (tmpInfo.dctype == "trade") {
											tmpInfo.showName = "交割"
										}
									}
								}
							}
						}
					}
					tmpInfo.showTime = "";
					if (JSUtils.string.isAvailably(tmpInfo.dctime)) {
						var tmpD = JSUtils.date.timeStrTODate(tmpInfo.dctime);
						tmpInfo.showTime = tmpD.format("yyyy-MM-dd hh:mm:ss")
					}
					tmpInfo.showMoney = "";
					if (tmpInfo.dcflag == "d") {
						tmpInfo.showMoney = "+ " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
					} else {
						if (tmpInfo.dcflag == "c") {
							tmpInfo.showMoney = "- " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
						}
					}
					$scope.dtlDCInfos.push(tmpInfo)
				}
			}
		}, null)
	}
	trade.dtlTopListsQuery("D", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.dayRange.push(temp)
				}
			}
		} else {}
	}, null);
	trade.dtlTopListsQuery("W", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.weekRange.push(temp)
				}
			}
		} else {}
	}, null);
	trade.dtlTopListsQuery("M", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.monthRange.push(temp)
				}
			}
		} else {}
	}, null);
	$scope.channelChange = function(index) {
		if (index == 0) {
			$scope.selectIndex = Number(index);
			document.getElementsByClassName("tradeDetaile")[0].style.display = "block";
			document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
			document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
		} else {
			if (index == 1) {
				$scope.selectIndex = Number(index);
				document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
				document.getElementsByClassName("moneyDetaile")[0].style.display = "block";
				document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
			} else {
				if (index == 2) {
					$scope.selectIndex = Number(index);
					document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
					document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
					document.getElementsByClassName("rangeDetaile")[0].style.display = "block"
				} else {
					if (index == 3) {
						$scope.selectIndex_range = Number(index);
						document.getElementsByClassName("rangeDetaile_day")[0].style.display = "block";
						document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
						document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
					} else {
						if (index == 4) {
							$scope.selectIndex_range = Number(index);
							document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
							document.getElementsByClassName("rangeDetaile_week")[0].style.display = "block";
							document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
						} else {
							if (index == 5) {
								$scope.selectIndex_range = Number(index);
								document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "block"
							}
						}
					}
				}
			}
		}
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("明细");
		tradeDetail();
		moneyDetail();
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("moneyPackContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.headimgurl = "";
	var defaultMoneySplit = JSUtils.number.moneySplitIntAndFloat(null);
	$scope.rmbmoneyInt = defaultMoneySplit[0];
	$scope.rmbmoneyFloat = defaultMoneySplit[1];
	$scope.redmoneyInt = defaultMoneySplit[0];
	$scope.redmoneyFloat = defaultMoneySplit[1];

	function setup(data) {
		var userInfo = data.userinfo;
		if (userInfo) {
			$scope.headimgurl = userInfo.headimgurl
		}
		var accountList = data.account;
		if (accountList) {
			for (var i = 0; i < accountList.length; i++) {
				var account = accountList[i];
				var splitMoney = JSUtils.number.moneySplitIntAndFloat(account.abalance);
				if (account.atype == "R") {
					$scope.rmbmoneyInt = splitMoney[0];
					$scope.rmbmoneyFloat = splitMoney[1]
				} else {
					if (account.atype == "V") {} else {
						if (account.atype == "C") {
							$scope.redmoneyInt = splitMoney[0];
							$scope.redmoneyFloat = splitMoney[1]
						}
					}
				}
			}
		}
	}
	$scope.withdrawCard = function(val) {
		if (val == 1) {
			$location.path("tabs/recharge")
		} else {
			if (val == 2) {
				$location.path("tabs/withdraw")
			} else {
				if (val == 3) {
					_MEIQIA("showPanel")
				}
			}
		}
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		queryUser();
		$ionicTabsDelegate.showBar(true)
	});

	function queryUser() {
		trade.userInfoQuery(function(data, state, error, srvJson) {
			if (error.errcode == "2000") {
				setup(srvJson)
			} else {}
		}, function(data, state) {})
	}
	trade.update_wx_title("钱包")
}]);
controllers.controller("moneyPackQuestionContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.submit = function() {
		var userPhone = $scope.userPhone;
		var payMoney = $scope.payMoney;
		var wxBillId = $scope.wxBillId;
		if (!JSUtils.string.isAvailably(userPhone)) {
			trade.alert("请输入手机");
			return
		}
		if (!JSUtils.string.isAvailably(payMoney)) {
			trade.alert("请输入正确充值金额");
			return
		}
		if (!JSUtils.string.isAvailably(wxBillId)) {
			trade.alert("请输入单号");
			return
		}
		payMoney = parseFloat(payMoney);
		if (payMoney <= 0) {
			trade.alert("请输入正确充值金额");
			return
		}
		var feedback = {
			type: 1,
			telnum: userPhone,
			amount: payMoney,
			billid01: wxBillId
		};
		trade.backFeed(feedback, function() {
			trade.alert("您反馈的信息我们会尽快处理！");
			$scope.userPhone = "";
			$scope.payMoney = "";
			$scope.wxBillId = ""
		}, function() {})
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("充值问题");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.afterEnter", function() {
		$ionicTabsDelegate.showBar(false)
	})
}]);
controllers.controller("moneyPackResultContrl", ["$scope", "$ionicTabsDelegate", function($scope, $ionicTabsDelegate) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("rechargeContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", "$timeout", function($scope, $ionicTabsDelegate, trade, $location, $timeout) {
	$scope.headimgurl = "";
	var defaultMoneySplit = JSUtils.number.moneySplitIntAndFloat(null);
	$scope.rmbmoneyInt = defaultMoneySplit[0];
	$scope.rmbmoneyFloat = defaultMoneySplit[1];
	$scope.redmoneyInt = defaultMoneySplit[0];
	$scope.redmoneyFloat = defaultMoneySplit[1];
	$scope.btnfinsh = "rechange_money_confirm_btn";
	$scope.payNum = 20;
	$scope.billId = "";
	var clickFlag = true;
	var queryPay = true;
	var queryPayTime = 180;
	var billId = "";
	$scope.cur_img_base64 = "";

	function setup(data) {
		var userInfo = data.userinfo;
		if (userInfo) {
			$scope.headimgurl = userInfo.headimgurl
		}
		var accountList = data.account;
		if (accountList) {
			for (var i = 0; i < accountList.length; i++) {
				var account = accountList[i];
				var splitMoney = JSUtils.number.moneySplitIntAndFloat(account.abalance);
				if (account.atype == "R") {
					$scope.rmbmoneyInt = splitMoney[0];
					$scope.rmbmoneyFloat = splitMoney[1]
				} else {
					if (account.atype == "V") {} else {
						if (account.atype == "C") {
							$scope.redmoneyInt = splitMoney[0];
							$scope.redmoneyFloat = splitMoney[1]
						}
					}
				}
			}
		}
	}
	$scope.rechange = function() {
		if (!clickFlag) {
			return
		}
		clickFlag = false;
		trade.showLoading();
		trade.etPayPara($scope.payNum, document.getElementById("test").value, function(data, state) {
			console.log("etPayPara  data=" + JSUtils.object.toJsonStr(data));
			trade.hideLoading();
			if (data.error["errcode"] == 2000) {
				var wxObj = data.data["wxpaypara"];
				$scope.billId = wxObj.billId;
				billId = wxObj.billId;
				$scope.cur_img_base64 = wxObj.qrBase64;
				queryPay = true;
				queryPayTime = 180;
				queryPayCashResult();
				document.getElementById("moneyPack").style.display = "none";
				document.getElementById("qrCode").style.display = "block";
				var length2 = document.getElementById("txt_length").offsetWidth;
				document.getElementById("img_width").style.width = Number(length2) + 50 + "px";
				var heiImg = document.getElementById("weixinheight").offsetHeight;
				console.log(heiImg);
				document.getElementById("imgheight").style.height = heiImg + "px"
			} else {
				clickFlag = true
			}
		}, function(data, state) {
			trade.hideLoading();
			clickFlag = true;
			document.getElementById("moneyPack").style.display = "block";
			document.getElementById("qrCode").style.display = "none"
		})
	};
	$scope.remoneyIndex = 0;
	$scope.remoneyChange = function(index, payNum) {
		$scope.remoneyIndex = index;
		$scope.payNum = payNum
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("充值");
		$ionicTabsDelegate.showBar(false);
		queryUser(null)
	});
	$scope.$on("$ionicView.afterEnter", function() {
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		queryPay = false;
		var path = $location.path();
		if ("/tabs/me" == path || "/tabs/trade" == path || "/tabs/moneyPack" == path) {
			$ionicTabsDelegate.showBar(true)
		} else {
			if (path.indexOf("/tabs/analogTrade/") >= 0) {
				$ionicTabsDelegate.showBar(false)
			}
		}
	});

	function queryPayCashResult() {
		if (queryPay) {
			trade.QueryPayCashResult(billId, function(data) {
				var dataObj = data.data;
				if (dataObj.flag == 200) {
					setup(data.data);
					document.getElementById("moneyPack").style.display = "block";
					document.getElementById("qrCode").style.display = "none";
					clickFlag = true
				} else {
					if (queryPayTime > 0) {
						queryPayTime--;
						$timeout(function() {
							queryPayCashResult()
						}, 1000)
					} else {
						document.getElementById("moneyPack").style.display = "block";
						document.getElementById("qrCode").style.display = "none";
						clickFlag = false;
						$scope.btnfinsh = "rechange_money_confirm_btn_gray";
						$scope.remoneyIndex = 10
					}
				}
			}, null)
		}
	}
	function queryUser(successFun) {
		trade.showLoading();
		trade.payPageInit(function(data, state) {
			console.log(data);
			if (data.error["errcode"] == "2000") {
				setup(data.data);
				if (successFun) {
					successFun()
				}
			} else {
				console.log(data.error["errmsg"]);
				clickFlag = false;
				$scope.btnfinsh = "rechange_money_confirm_btn_gray";
				$scope.remoneyIndex = 10
			}
			trade.hideLoading()
		}, function(data, state) {
			trade.hideLoading()
		})
	}
}]);
controllers.controller("withdrawContrl", ["$scope", "$ionicTabsDelegate", "trade", "$timeout", function($scope, $ionicTabsDelegate, trade, $timeout) {
	$scope.abalanceNum = 0;
	$scope.abalance = "0.00";
	$scope.btnfinsh = "withdraw_money_confirm_btn";
	var lockBtnFlag = true;
	var queryPay = true;
	var queryPayTime = 180;
	var billId = "";

	function setup(data) {
		var accountList = data.account;
		document.getElementById("moneyinput").value = "";
		document.getElementById("moneyinput").placeholder = "请输入金额";
		if (accountList) {
			for (var i = 0; i < accountList.length; i++) {
				var account = accountList[i];
				if (account.atype == "R") {
					$scope.abalance = JSUtils.number.formatNumeric(account.abalance, 2);
					$scope.abalanceNum = parseFloat(account.abalance);
					break
				}
			}
		}
	}
	$scope.confirm = function() {
		if (!lockBtnFlag) {
			return
		}
		lockBtnFlag = false;
		var moneyinputNum = parseFloat(document.getElementById("moneyinput").value);
		if (isNaN(moneyinputNum) || moneyinputNum <= 0) {
			trade.alert("请输入正确的提现金额");
			lockBtnFlag = true;
			return
		}
		if (moneyinputNum > $scope.abalanceNum) {
			trade.alert("余额不足");
			lockBtnFlag = true;
			return
		}
		var confirmPopup = trade.confirm("", "您确定需要提现" + JSUtils.number.formatNumeric(moneyinputNum, 2) + "元吗？");
		confirmPopup.then(function(res) {
			if (res) {
				getSrvCachMoney(moneyinputNum)
			} else {
				lockBtnFlag = true
			}
		})
	};

	function getSrvCachMoney(money) {
		trade.showLoading();
		trade.getCashBackPara(money, document.getElementById("test").value, function(data, state, error, srvJson) {
			if (error.errcode == 2000) {
				queryPay = true;
				queryPayTime = 180;
				billId = data.data["wxcashbackpara"]["billId"];
				queryPayCashResult()
			} else {
				trade.hideLoading();
				lockBtnFlag = true
			}
		}, function() {
			trade.hideLoading();
			lockBtnFlag = true
		})
	}
	function getSrvInit() {
		trade.cashBackPageInit(function(data, state, error, srvJson) {
			setup(srvJson);
			if (error.errcode != 2000) {
				lockBtnFlag = false;
				$scope.btnfinsh = "rechange_money_confirm_btn_gray"
			}
		}, function() {})
	}
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("提现");
		$scope.isOpen = true;
		getSrvInit();
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		queryPay = false;
		trade.hideLoading();
		$ionicTabsDelegate.showBar(true)
	});

	function queryPayCashResult() {
		if (queryPay) {
			trade.QueryPayCashResult(billId, function(data) {
				var dataObj = data.data;
				if (dataObj.flag == 200) {
					setup(data.data);
					trade.alert("提现成功");
					lockBtnFlag = true;
					trade.hideLoading()
				} else {
					if (queryPayTime > 0) {
						queryPayTime--;
						$timeout(function() {
							queryPayCashResult()
						}, 1000)
					} else {
						lockBtnFlag = false;
						trade.hideLoading();
						$scope.btnfinsh = "rechange_money_confirm_btn_gray"
					}
				}
			}, function() {
				lockBtnFlag = false;
				trade.hideLoading()
			})
		}
	}
}]);
controllers.controller("QRcodeContrl", ["$scope", "$ionicTabsDelegate", "trade", function($scope, $ionicTabsDelegate, trade) {
	self.touchStart = function(e) {
		self.startCoordinates = getPointerCoordinates(e);
		if (ionic.tap.ignoreScrollStart(e)) {
			return
		}
		if (ionic.tap.containsOrIsTextInput(e.target)) {
			self.__hasStarted = false;
			return
		}
		self.__isSelectable = true;
		self.__enableScrollY = true;
		self.__hasStarted = true;
		self.doTouchStart(e.touches, e.timeStamp)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("loginContrl", ["$scope", "$ionicTabsDelegate", "trade", "$location", function($scope, $ionicTabsDelegate, trade, $location) {
	$scope.goBackHome = function() {
		var path = $location.path().split("login_")[1];
		var urlhome = "";
		if (path == "money") {
			urlhome = "tabs/moneyPack"
		} else {
			urlhome = "tabs/" + path
		}
		console.log(urlhome);
		$location.path(urlhome)
	};
	$scope.help = function() {
		var path = $location.path().split("login_")[1];
		var urlhelp = "tabs/rulepage_" + path;
		console.log(urlhelp);
		$location.path(urlhelp)
	};
	$scope.freeRegister = function() {
		var path = $location.path().split("login_")[1];
		var url = "tabs/register_" + path;
		$location.path(url)
	};
	$scope.paswbtn = function() {
		var path = $location.path().split("login_")[1];
		var url = "tabs/newCode_" + path;
		$location.path(url)
	};
	$scope.submit = function() {
		var phone = document.getElementById("login_phone").value;
		var psw = document.getElementById("login_pass").value;
		if (phone == "") {
			trade.alert("请填写手机号码");
			return
		}
		if (phone.length != 11) {
			trade.alert("手机号码必须为11位");
			return
		}
		if (psw == "") {
			trade.alert("请输入密码");
			return
		}
		trade.UserLogin(phone, psw, function(data) {
			console.log(data);
			if (data.error["errcode"] == "2000") {
				if (data.data["userinfo"]["sessionid"]) {
					console.log("保存sid");
					JSUtils.curApp.sessionObj.put("sessionId", data.data["userinfo"]["sessionid"])
				}
				var url = $location.path().split("tabs/login_")[1];
				var urlhome = "";
				if (url == "money") {
					urlhome = "tabs/moneyPack"
				} else {
					urlhome = "tabs/" + url
				}
				if (data.error["errcode"] == "2000") {
					console.log(urlhome);
					$location.path(urlhome)
				}
			}
		}, null)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		var urltabs = $location.path().split("tabs/")[1];
		if (urltabs == "trade" || urltabs == "money" || urltabs == "discover" || urltabs == "me") {
			$ionicTabsDelegate.showBar(true)
		}
	})
}]);
controllers.controller("newCodeContrl", ["$scope", "$ionicTabsDelegate", "trade", "$timeout", "$location", function($scope, $ionicTabsDelegate, trade, $timeout, $location) {
	$scope.VerificationCode = "获取验证码";
	$scope.verificationNum = 60;
	$scope.verificationFlag = false;
	$scope.isActive = true;
	$scope.input = {
		phone: ""
	};
	$scope.input = {
		QRcode: ""
	};
	$scope.input = {
		psw1: ""
	};
	$scope.input = {
		psw2: ""
	};
	$scope.input.phone = "";
	$scope.input.QRcode = "";
	$scope.input.psw1 = "";
	$scope.input.psw2 = "";
	$scope.help = function() {
		var path = $location.path().split("register_")[1];
		var url = "tabs/rulepage_" + path;
		$location.path(url)
	};
	$scope.sendVerfiCode = function() {
		var phone = document.getElementById("mePhoneNum").value;
		console.log(phone);
		if ($scope.verificationFlag) {
			return
		}
		if (phone == "") {
			trade.alert("请填写手机号码");
			return
		}
		if (phone.length != 11) {
			trade.alert("手机号码必须为11位");
			return
		}
		$scope.verificationFlag = true;
		trade.ForgetGetChkCode(phone, function(data) {
			console.log(data);
			if (data.error["errcode"] == "1201" || data.error["errcode"] == "2000") {
				$scope.isActive = false;
				$scope.VerificationCode = 60;
				countDown(60)
			} else {
				$scope.verificationFlag = false
			}
		}, function(data) {
			console.log(data)
		})
	};
	$scope.registerbtn = function() {
		var phone = document.getElementById("mePhoneNum").value;
		var QRcode = document.getElementById("yzhm").value;
		var psw1 = document.getElementById("password").value;
		var psw2 = document.getElementById("yzmm").value;
		if (phone == "") {
			trade.alert("请填写手机号码");
			return
		}
		if (phone.length != 11) {
			trade.alert("手机号码必须为11位");
			return
		}
		if (QRcode == "") {
			trade.alert("请输入验证码");
			return
		}
		if (psw1 == "") {
			trade.alert("请输入密码");
			return
		}
		if (psw1.length < 6) {
			trade.alert("密码至少为6位");
			return
		}
		if (psw1 != psw2) {
			trade.alert("两次密码不相同，请重新输入");
			return
		}
		trade.ForgetSave(phone, QRcode, psw1, function(data) {
			if (data.data["sessionid"]) {
				JSUtils.curApp.sessionObj.put("sessionId", data.data["sessionid"])
			}
			if (data.error["errcode"] == "2000") {
				window.history.go(-1)
			}
		}, null)
	};

	function countDown(num) {
		if (num >= 0) {
			$timeout(function() {
				num--;
				$scope.VerificationCode = num;
				countDown(num)
			}, 1000)
		} else {
			$scope.verificationFlag = false;
			$scope.isActive = true;
			$scope.VerificationCode = "获取验证码"
		}
	}
	$scope.$on("$ionicView.beforeEnter", function() {
		console.log("忘记密码");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(false)
	})
}]);
controllers.controller("registerContrl", ["$scope", "$ionicTabsDelegate", "trade", "$timeout", "$location", function($scope, $ionicTabsDelegate, trade, $timeout, $location) {
	$scope.VerificationCode = "获取验证码";
	$scope.verificationNum = 60;
	$scope.verificationFlag = false;
	$scope.isActive = true;
	$scope.input = {
		phone: ""
	};
	$scope.input = {
		QRcode: ""
	};
	$scope.input = {
		psw1: ""
	};
	$scope.input = {
		psw2: ""
	};
	$scope.input.phone = "";
	$scope.input.QRcode = "";
	$scope.input.psw1 = "";
	$scope.input.psw2 = "";
	$scope.help = function() {
		var path = $location.path().split("register_")[1];
		var url = "tabs/rulepage_" + path;
		$location.path(url)
	};
	$scope.sendVerfiCode = function() {
		console.log($scope.input.phone);
		var phone = document.getElementById("mePhoneNum").value;
		console.log(phone);
		if ($scope.verificationFlag) {
			return
		}
		if (phone == "") {
			trade.alert("请填写手机号码");
			return
		}
		if (phone.length != 11) {
			trade.alert("手机号码必须为11位");
			return
		}
		$scope.verificationFlag = true;
		trade.RegGetCheckCode(phone, function(data) {
			console.log(data);
			if (data.error["errcode"] == "1201" || data.error["errcode"] == "2000") {
				$scope.isActive = false;
				$scope.VerificationCode = 60;
				countDown(60)
			} else {
				$scope.verificationFlag = false
			}
		}, null)
	};
	$scope.registerbtn = function() {
		var phone = document.getElementById("mePhoneNum").value;
		var QRcode = document.getElementById("yzhm").value;
		var psw1 = document.getElementById("password").value;
		var psw2 = document.getElementById("yzmm").value;
		if (phone == "") {
			trade.alert("请填写手机号码");
			return
		}
		if (phone.length != 11) {
			trade.alert("手机号码必须为11位");
			return
		}
		if (QRcode == "") {
			trade.alert("请输入验证码");
			return
		}
		if (psw1 == "") {
			trade.alert("请输入密码");
			return
		}
		if (psw1.length < 6) {
			trade.alert("密码长度最少为6位");
			return
		}
		if (psw1 != psw2) {
			trade.alert("两次密码不相同，请重新输入");
			return
		}
		trade.RegSave(phone, QRcode, psw1, function(data) {
			var urlhome = "";
			var url = $location.path().split("tabs/register_")[1];
			if (url == "money") {
				urlhome = "tabs/moneyPack"
			} else {
				urlhome = "tabs/" + url
			}
			if (data.error["errcode"] == "2000") {
				if (data.data["userinfo"]["sessionid"]) {
					JSUtils.curApp.sessionObj.put("sessionId", data.data["userinfo"]["sessionid"])
				}
				$location.path(urlhome)
			}
		}, null)
	};

	function countDown(num) {
		if (num >= 0) {
			$timeout(function() {
				num--;
				$scope.VerificationCode = num;
				countDown(num)
			}, 1000)
		} else {
			$scope.verificationFlag = false;
			$scope.isActive = true;
			$scope.VerificationCode = "获取验证码"
		}
	}
	$scope.$on("$ionicView.beforeEnter", function() {
		console.log("注册");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		var urltabs = $location.path().split("tabs/")[1];
		if (urltabs == "trade" || urltabs == "money" || urltabs == "discover" || urltabs == "me") {
			$ionicTabsDelegate.showBar(true)
		}
	})
}]);
controllers.controller("agentContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.selectIndex = 0;
	$scope.selectIndex_range = 3;
	$scope.dtlTrades = [];
	$scope.dtlDCInfos = [];
	$scope.dayRange = [];
	$scope.weekRange = [];
	$scope.monthRange = [];
	$scope.channelChange = function(index) {
		if (index == 0) {
			$scope.selectIndex = Number(index);
			document.getElementsByClassName("tradeDetaile")[0].style.display = "block";
			document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
			document.getElementsByClassName("agent_data")[0].style.display = "none"
		} else {
			if (index == 1) {
				$scope.selectIndex = Number(index);
				document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
				document.getElementsByClassName("moneyDetaile")[0].style.display = "block";
				document.getElementsByClassName("agent_data")[0].style.display = "none"
			} else {
				if (index == 2) {
					$scope.selectIndex = Number(index);
					document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
					document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
					document.getElementsByClassName("agent_data")[0].style.display = "block"
				}
			}
		}
	};
	$scope.invite = function() {
		if ($location.path() == "/tabs/discover") {
			$location.path("tabs/invite_discover")
		} else {
			$location.path("tabs/invite_trade")
		}
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("代理赚钱");
		if ($location.path() != "/tabs/discover") {
			$ionicTabsDelegate.showBar(false)
		} else {
			$ionicTabsDelegate.showBar(true)
		}
	});
	$scope.$on("$ionicView.leave", function() {
		if ($location.path() != "/tabs/discover") {
			if ($location.path() == "/tabs/invite_trade" || $location.path() == "/tabs/invite_discover") {
				$ionicTabsDelegate.showBar(false)
			} else {
				$ionicTabsDelegate.showBar(true)
			}
		}
	})
}]);
controllers.controller("inviteContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.inviteImg = "";
	var base64 = [];
	var imgData = [];
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("邀请");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		if ($location.path() == "/tabs/discover") {
			$ionicTabsDelegate.showBar(true)
		} else {
			$ionicTabsDelegate.showBar(false)
		}
	});

	function draw(fn) {
		var c = document.createElement("canvas");
		var ctx = c.getContext("2d");
		console.log("data = " + data);
		var len = data.length;
		c.width = 960;
		c.height = 1704;
		ctx.rect(0, 0, c.width, c.height);
		ctx.fillStyle = "#fff";
		ctx.fill();

		function drawing(n) {
			if (n < len) {
				var img = new Image;
				img.src = data[n];
				img.onload = function() {
					if (n == 1) {
						ctx.drawImage(img, 231, 651, 496, 496)
					} else {
						ctx.drawImage(img, 0, 0, 960, 1704)
					}
					drawing(n + 1)
				}
			} else {
				$scope.inviteImg = c.toDataURL("image/jpeg", 0.8)
			}
		}
		drawing(0)
	}
}]);
controllers.controller("analogTradeContrl", ["$scope", "$ionicTabsDelegate", "$location", "trade", "$ionicPopup", "$stateParams", "$timeout", function($scope, $ionicTabsDelegate, $location, trade, $ionicPopup, $stateParams, $timeout) {
	var markP = [];
	var markL = [];
	var xmax;
	var ymax;
	var xmaxadd;
	var echart_right = 50;
	$scope.trade_type = "";
	$scope.btnname = "";
	$scope.usertradenickname = "";
	$scope.usertrattargetid = "";
	$scope.usertratorderdir = "";
	$scope.usertrattradeprofit = "";
	$scope.accountMoney = 0;
	$scope.name = 2;
	$scope.orderMoney = 1;
	$scope.moneytol = [];
	$scope.vidarr = [];
	$scope.vid = "";
	$scope.vinvarr = [];
	$scope.vinv = [];
	$scope.vratearr = [];
	$scope.vrate = "";
	$scope.getMoney = Number($scope.orderMoney) * Number($scope.vrate) / 100;
	var order_num_limit_min = 1;
	var order_num_limit_max = 1;
	var everyMoney = 1;
	$scope.nowPlice = "";
	var data_trade = [];
	var targetid = "";
	var type = "";
	var timess = "";
	var indexinit = true;
	var salemarket = false;
	var qurerymarket = false;
	var horn = "1";
	var hornNum = 2;
	$scope.cLList = [];
	$scope.tradeList = [];
	$scope.money = 0;
	$scope.moneyselect = function(index) {
		$scope.orderMoney = index;
		document.getElementById("money_list_box").style.display = "block"
	};
	$scope.open = function() {
		if (document.getElementById("money_list_box").style.display == "none") {
			document.getElementById("money_list_box").style.display = "block"
		} else {
			document.getElementById("money_list_box").style.display = "none"
		}
	};
	var cycleChangeNum = 0;
	$scope.cycleChange = function(val) {
		if (val == "+") {
			cycleChangeNum++;
			if (cycleChangeNum < $scope.vinvarr.length) {
				$scope.vid = $scope.vidarr[cycleChangeNum];
				$scope.vinv = $scope.vinvarr[cycleChangeNum];
				$scope.vrate = $scope.vratearr[cycleChangeNum] * 100
			} else {
				cycleChangeNum = $scope.vinvarr.length - 1;
				return
			}
		} else {
			if (val == "-") {
				cycleChangeNum--;
				if (cycleChangeNum < 0) {
					cycleChangeNum = 0;
					return
				} else {
					$scope.vid = $scope.vidarr[cycleChangeNum];
					$scope.vinv = $scope.vinvarr[cycleChangeNum];
					$scope.vrate = $scope.vratearr[cycleChangeNum] * 100
				}
			}
		}
		$scope.getMoney = JSUtils.number.moneySplitIntAndFloat(Number($scope.orderMoney) * Number($scope.vrate) / 100 * everyMoney).join(".")
	};
	$scope.orderMoneyChange = function(val) {
		if (val == "+") {
			if ($scope.orderMoney >= order_num_limit_max) {
				return
			} else {
				$scope.orderMoney++
			}
		} else {
			if (val == "-") {
				if ($scope.orderMoney <= order_num_limit_min) {
					return
				} else {
					$scope.orderMoney--
				}
			}
		}
		$scope.getMoney = JSUtils.number.moneySplitIntAndFloat(Number($scope.orderMoney) * Number($scope.vrate) / 100 * everyMoney).join(".")
	};
	$scope.analog_withdrawCard = function() {
		if ($scope.btnname == "充值") {
			$location.path("tabs/chongzhi_trade")
		} else {
			var urlstr = window.location.href;
			var urlstrarr = urlstr.split("/analogTrade_trade/");
			urlstrarr[1] = ":btc_cny/:2";
			window.location.href = urlstrarr.join("/analogTrade_trade/")
		}
	};
	var zdLockFlag = false;
	$scope.trade_order_action = function(val) {
		if (val == 3) {
			document.getElementsByClassName("atrade_content")[0].style.display = "none";
			document.getElementsByClassName("atrade_order_result")[0].style.display = "block";
			InitMarketTrade()
		} else {
			if (val == 4) {
				document.getElementsByClassName("atrade_content")[0].style.display = "block";
				document.getElementsByClassName("atrade_order_result")[0].style.display = "none";
				$scope.cLList = [];
				submark(null)
			} else {
				if (val == 1 || val == 2) {
					if (zdLockFlag) {
						trade.alert("下单过于频繁，请稍候！");
						return
					}
					zdLockFlag = true;
					var trades = {};
					trades.targetid = targetid;
					trades.interval = $scope.vinv;
					trades.orderdir = val;
					trades.ordernum = $scope.orderMoney;
					trades.type = type;
					trade.makeTrade(trades, function(data, state) {
						if (data.error["errcode"] == "2000") {
							localStorage.setItem("account", JSUtils.object.toJsonStr(data.data["account"]));
							var temp = data.data["account"];
							if (type == "V") {
								for (var i = 0; i < temp.length; i++) {
									if (temp[i]["atype"] == "V") {
										$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(temp[i]["abalance"]).join(".")
									}
								}
							} else {
								var moneytol = 0;
								for (var i = 0; i < temp.length; i++) {
									if (temp[i]["atype"] != "V") {
										moneytol += Number(temp[i]["abalance"])
									}
								}
								$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(moneytol).join(".")
							}
							document.getElementsByClassName("atrade_content")[0].style.display = "none";
							document.getElementsByClassName("atrade_order_result")[0].style.display = "block";
							InitMarketTrade()
						}
					}, null);
					window.setTimeout(function() {
						zdLockFlag = false
					}, 3000)
				}
			}
		}
	};

	function InitMarketTrade() {
		$scope.cLList = [];
		trade.InitMarketTrade(targetid, type, timess, function(data, status) {
			var trades = data.data["trades"];
			var timess = data.data["timess"];
			if (trades.length > 0) {
				document.getElementById("none").style.display = "none";
				document.getElementsByClassName("atrade_order_second")[0].style.display = "block";
				chicanglist(trades, timess, true)
			} else {
				document.getElementById("none").style.display = "flex";
				document.getElementsByClassName("atrade_order_second")[0].style.display = "none"
			}
		}, null)
	}
	function TargetMarketTrade(billid, resultShowCheck) {
		trade.TargetMarketTrade(billid, function(data, status) {
			var data = data.data;
			var ret = data.ret;
			var inv = data.inv;
			var accounts = data.account;
			if (ret == "0") {
				$scope.cLList[resultShowCheck]["countDownFlag"] = true
			} else {
				var trade = data.trade;
				var accounts = data.account;
				var abalanceV = 0;
				var abalanceR = 0;
				for (var i = 0; i < accounts.length; i++) {
					var account = accounts[i];
					if (account.atype == "V") {
						abalanceV = account.abalance
					} else {
						abalanceR += account.abalance
					}
				}
				if (type == "V") {
					$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(abalanceV).join(".")
				} else {
					$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(abalanceR).join(".")
				}
				submark(trade.billid);
				$scope.tradeList.push(trade);
				for (var j = 0; j < $scope.cLList.length; j++) {
					if ($scope.cLList[j]["billid"] == billid) {
						$scope.cLList[j]["billidResult"] = true;
						$scope.cLList[j]["billidAlert"] = false;
						$scope.cLList[j]["tradeprice"] = trade.tradeprice;
						$scope.cLList[j]["orderdir"] = trade.orderdir;
						$scope.cLList[j]["tprofitflag"] = trade.tprofitflag;
						$scope.cLList[j]["tradeprofit"] = trade.tradeprofit;
						if (trade.tprofitflag == 1) {
							$scope.cLList[j]["resultPrice"] = trade.tradeprofit
						} else {
							if (trade.tprofitflag == 2) {
								$scope.cLList[j]["resultPrice"] = trade.stakesum
							}
						}
					}
				}
				$scope.test = false;
				noticeResultFun(trade.billid)
			}
		}, null)
	}
	$scope.noticeResult = function(billidR) {
		var billidResult = document.getElementById(billidR + "_result");
		billidResult.parentNode.style.display = "none";
		var doc = document.getElementsByClassName("atrade_order_first");
		var docnum = 0;
		for (var i = 0; i < doc.length; i++) {
			if (doc[i]["style"]["display"] == "none") {
				docnum++
			}
		}
		if (docnum == doc.length) {
			document.getElementById("none").style.display = "flex";
			document.getElementsByClassName("atrade_order_second")[0].style.display = "none"
		}
	};

	function noticeResultFun(billidR) {
		trade.ViewMarketTrade([billidR], function(data, status) {}, null)
	}
	var myChart;

	function timeZero(p) {
		if (p < 10) {
			return "0" + p
		}
		return p
	}
	function getChartValue(nowTime, value) {
		return {
			name: nowTime.toString(),
			value: [nowTime.toString(), value]
		}
	}
	function chicanglist(trades, timess, marketFlag) {
		var timessDate = JSUtils.date.timeStrTODate(timess);
		var tempArrList = [];
		for (var i = 0; i < trades.length; i++) {
			var temparr = {};
			temparr.interval = trades[i].interval;
			temparr.billid = trades[i].billid;
			temparr.targetid = trades[i]["targetid"];
			temparr.tnoticeflag = trades[i].tnoticeflag;
			temparr.tradetimeexpect = trades[i]["tradetimeexpect"];
			var ordertime = trades[i]["ordertime"];
			var tradetimeexpectDate = JSUtils.date.timeStrTODate(temparr.tradetimeexpect);
			temparr.countCownTime = Number((tradetimeexpectDate - timessDate) / 1000);
			temparr.countDownFlag = false;
			temparr.ordertime = ordertime;
			temparr.timeresult = temparr.tradetimeexpect.substr(8, 2) + ":" + temparr.tradetimeexpect.substr(10, 2) + ":" + temparr.tradetimeexpect.substr(12, 2);
			temparr.tradeprofit = trades[i]["tradeprofit"];
			temparr.tradeprice = JSUtils.number.moneySplitIntAndFloat(trades[i].tradeprice).join(".");
			if (trades[i].orderdir == "1") {
				temparr.orderdir = "看跌";
				temparr.orderdirflag = "1"
			} else {
				if (trades[i].orderdir == "2") {
					temparr.orderdir = "看涨";
					temparr.orderdirflag = "2"
				}
			}
			if ($scope.trade_type == "1") {
				temparr.orderprice = JSUtils.number.fiveFloat(trades[i].orderprice).join(".")
			} else {
				temparr.orderprice = JSUtils.number.moneySplitIntAndFloat(trades[i].orderprice).join(".")
			}
			temparr.stakesum = trades[i].stakesum;
			if (trades[i].tprofitflag == "0") {
				temparr.tprofitflag = "未交割";
				temparr.tproflag = "0";
				if (marketFlag) {
					addmark(ordertime, trades[i].orderprice, trades[i].orderdir, trades[i].billid)
				}
			} else {
				if (trades[i].tprofitflag == "1") {
					temparr.tprofitflag = "盈利";
					temparr.tproflag = "1"
				} else {
					if (trades[i].tprofitflag == "2") {
						temparr.tprofitflag = "亏损";
						temparr.tproflag = "2"
					} else {
						if (trades[i].tprofitflag == "3") {
							temparr.tprofitflag = "打平";
							temparr.tproflag = "3"
						} else {
							if (trades[i].tprofitflag == "4") {
								temparr.tprofitflag = "异常";
								temparr.tproflag = "4"
							}
						}
					}
				}
			}
			temparr.clearResultTime = Number(-1);
			if (temparr.countCownTime <= 0) {
				temparr.billidResult = true;
				temparr.billidAlert = false
			} else {
				temparr.billidResult = false;
				temparr.billidAlert = true
			}
			if (trade.tprofitflag == 1) {
				$scope.cLList[j]["resultPrice"] = trade.tradeprofit
			} else {
				if (trade.tprofitflag == 2) {
					$scope.cLList[j]["resultPrice"] = trade.stakesum
				}
			}
			tempArrList.push(temparr)
		}
		$scope.cLList = tempArrList.sort(function(a, b) {
			return b.ordertime - a.ordertime
		})
	}
	function addmark(x_num, y_num, dir, billid) {
		var xaxis = JSUtils.date.timeStrTODate(x_num);
		if (dir == 2) {
			var line_color = "#eb1a28";
			var kline_color = "#ee5859"
		} else {
			if (dir == 1) {
				var line_color = "#3BDDC5";
				var kline_color = "#3BDDC5"
			}
		}
		var markpoints = {
			symbol: "pin",
			name: billid,
			symbolSize: 50,
			coord: [xaxis, y_num],
			label: {
				normal: {
					show: true,
					formatter: (function() {
						if (line_color == "#3BDDC5") {
							return "看跌"
						} else {
							return "看涨"
						}
					})()
				}
			},
			itemStyle: {
				normal: {
					color: line_color
				}
			}
		};
		markP.push(markpoints);
		var axisyvalue = {
			name: billid,
			yAxis: y_num,
			lineStyle: {
				normal: {
					color: kline_color
				}
			}
		};
		markL.push(axisyvalue)
	}
	function submark(billid) {
		var arrIndex;
		if (billid == "" || billid == null) {
			markL = [];
			markP = []
		} else {
			for (var i = 0; i < markP.length; i++) {
				if (billid == markP[i]["name"]) {
					arrIndex = i;
					markL.splice(arrIndex, 1);
					markP.splice(arrIndex, 1);
					break
				}
			}
		}
	}
	var singleNum = 0;
	var hornNumtimes = 1;

	function singleT() {
		if ($scope.sycleCtrl) {
			JSUtils.system.addHandler(window, "online", function() {
				$scope.sycleCtrl = true;
				window.location.reload()
			});
			JSUtils.system.addHandler(window, "offline", function() {
				$scope.sycleCtrl = false
			});
			var tradeTop = document.getElementById("tradeTop");
			trade.singleTargetQuery(targetid, timess, horn, function(data, state) {
				var markets = data.data["markets"];
				if (markets.length > 0 && markets[0]["isclose"] == "1") {
					document.getElementById("btn_1").style.visibility = "hidden";
					document.getElementById("btn_2").style.visibility = "hidden";
					trade.alert("该交易品处于休市状态", "", function() {
						$location.path("tabs/trade")
					});
					return
				}
				var usertrades = data.data["usertrades"];
				if (usertrades) {
					horn = "0";
					broadcast(usertrades)
				}
				if (markets) {
					marketList(markets)
				}
			}, null)
		}
	}
	function broadcast(usertrade) {
		$scope.usertradenickname = "“" + usertrade.nickname + "”盈利";
		$scope.usertrattargetid = usertrade.ttargetid;
		$scope.usertratorderdir = usertrade.torderdir;
		$scope.usertrattradeprofit = " " + JSUtils.number.moneySplitIntAndFloat(usertrade.ttradeprofit).join(".") + " 元"
	}
	var xmaxaddnum;

	function xaxismax(str) {
		var str = str.substr(0, 12);
		if (xmaxaddnum) {
			if (xmaxaddnum > Number(str)) {
				return (xmaxaddnum + 1) * 100
			} else {
				xmaxaddnum = Number(str);
				return (xmaxaddnum + 1) * 100
			}
		} else {
			xmaxadd = Number(str);
			xmaxaddnum = xmaxadd;
			return (xmaxadd + 1) * 100
		}
	}
	function marketList(datas) {
		var tmpObj = "";
		var tmpDate = "";
		if (data_trade.length <= 0) {
			timess = datas[0]["timess"];
			for (var i = datas.length - 1; i >= 0; i--) {
				tmpObj = datas[i];
				tmpDate = JSUtils.date.timeStrTODate(tmpObj.timess);
				data_trade.push(getChartValue(tmpDate, tmpObj.pricetrade))
			}
		} else {
			if (datas.length <= 0) {} else {
				var oldtimess = timess;
				timess = datas[0]["timess"];
				xmaxadd = JSUtils.date.timeStrTODate(xaxismax(timess) + "");
				for (var i = datas.length - 1; i >= 0; i--) {
					if (Number(oldtimess) < Number(datas[i]["timess"])) {
						tmpObj = datas[i];
						tmpDate = JSUtils.date.timeStrTODate(tmpObj.timess);
						data_trade.shift();
						data_trade.push(getChartValue(tmpDate, tmpObj.pricetrade))
					}
				}
			}
		}
		ymax = data_trade[data_trade.length - 1]["value"][1];
		if ($scope.trade_type == "1") {
			$scope.nowplice = JSUtils.number.fiveFloat(ymax).join(".")
		} else {
			$scope.nowplice = JSUtils.number.moneySplitIntAndFloat(ymax).join(".")
		}
		xmax = data_trade[data_trade.length - 1]["value"][0];
		var option = {
			grid: {
				top: "40",
				left: "17",
				right: echart_right,
				bottom: "20"
			},
			backgroundColor: "rgb(24,25,30)",
			xAxis: {
				type: "time",
				splitNumber: 5,
				axisLabel: {
					show: true,
					textStyle: {
						color: "rgb(155,155,155)"
					},
					formatter: function(value, index) {
						var d = new Date();
						d.setTime(value);
						return timeZero(d.getMinutes()) + ":" + timeZero(d.getSeconds())
					},
					interval: function(index, value) {
						return true
					}
				},
				axisLine: {
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "dotted",
						width: 1
					}
				},
				axisTick: {
					show: true,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "solid",
						width: 1
					}
				},
				splitLine: {
					show: true,
					interval: 100,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "dotted",
						width: 1
					}
				}
			},
			yAxis: [{
				type: "value",
				scale: true,
				show: true,
				position: "right",
				splitNumber: 5,
				boundaryGap: [0.01, 0.01],
				axisLine: {
					show: false
				},
				splitLine: {
					show: true,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "dotted",
						width: 1
					}
				},
				axisLabel: {
					show: true,
					textStyle: {
						color: "rgb(155,155,155)"
					},
					formatter: function(value, index) {
						if ($scope.trade_type == "1") {
							return JSUtils.number.fiveFloat(value).join(".")
						} else {
							return JSUtils.number.moneySplitIntAndFloat(value).join(".")
						}
					},
					interval: function(index, value) {
						return true
					}
				}
			}],
			series: [{
				name: "模拟数据",
				type: "line",
				symbol: true,
				showSymbol: false,
				symbolSize: 0,
				hoverAnimation: false,
				data: data_trade,
				itemStyle: {
					normal: {
						color: "rgb(145,166,35)",
						type: "solid",
						borderWidth: 1,
						lineStyle: {
							type: "solid",
							width: 1
						}
					}
				},
				markPoint: {
					show: true,
					data: (function() {
						var aar = [{
							name: "固定 x 像素位置",
							symbol: "emptyCircle",
							symbolSize: 5,
							coord: [xmax, ymax],
							label: {
								normal: {
									show: false
								}
							}
						}];
						if (markP.length <= 0) {
							return aar
						} else {
							return aar.concat(markP)
						}
					})()
				},
				markLine: {
					symbol: ["none", "none"],
					precision: 5,
					data: (function() {
						var axisy = [{
							yAxis: ymax,
							xAxis: 0,
							value: ymax,
							label: {
								normal: {
									formatter: function(data) {
										if ($scope.trade_type == "1") {
											return JSUtils.number.fiveFloat(ymax).join(".")
										} else {
											return JSUtils.number.moneySplitIntAndFloat(ymax).join(".")
										}
									}
								}
							},
							lineStyle: {
								normal: {
									color: "#007aff"
								}
							}
						}];
						if (markL.length <= 0) {
							return axisy
						} else {
							return axisy.concat(markL)
						}
					})(),
					animation: false,
				}
			}]
		};
		if (option && typeof option === "object") {
			myChart.setOption(option, true)
		}
		$timeout(function() {
			if (hornNumtimes == hornNum) {
				horn = "1";
				hornNumtimes = 0
			} else {
				horn = "0";
				hornNumtimes++
			}
			var countCownTimeList = $scope.cLList;
			for (var i = 0; i < countCownTimeList.length; i++) {
				var countCownTime = Number(countCownTimeList[i]["countCownTime"]);
				var countDownFlag = countCownTimeList[i]["countDownFlag"];
				var billid = $scope.cLList[i].billid;
				countCownTime = countCownTime - 1;
				if (countCownTime < 0) {
					if (countCownTime <= -2) {
						$scope.cLList[i].countCownTime = "等待交割"
					}
				} else {
					if (!countDownFlag) {
						$scope.cLList[i].countCownTime = countCownTime
					} else {
						$scope.cLList[i].countCownTime = "等待交割"
					}
				}
				if (countCownTime <= 0) {
					TargetMarketTrade(billid, i);
					$scope.cLList[i].clearResultTime = Number(10)
				}
			}
			singleT()
		}, 1000)
	}
	$scope.$on("$ionicView.beforeEnter", function() {
		$scope.trade_type = ($stateParams.type).substr(1);
		$scope.tradeList = [];
		$scope.sycleCtrl = true;
		if ($scope.trade_type == "1") {
			echart_right = "70"
		} else {
			echart_right = "60"
		}
		myChart = echarts.init(document.getElementById("main"));
		myChart.setOption({
			grid: {
				top: "40",
				left: "17",
				right: echart_right,
				bottom: "20"
			},
			backgroundColor: "rgb(24,25,30)",
			xAxis: {
				type: "time",
				splitNumber: 5,
				axisLabel: {
					show: true,
					textStyle: {
						color: "rgb(155,155,155)"
					},
					formatter: function(value, index) {
						var d = new Date();
						d.setTime(value);
						return timeZero(d.getMinutes()) + ":" + timeZero(d.getSeconds())
					},
					interval: function(index, value) {
						return true
					}
				},
				axisLine: {
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "solid",
						width: 1
					}
				},
				axisTick: {
					show: true,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "solid",
						width: 1
					}
				},
				splitLine: {
					show: true,
					interval: 100,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "dotted",
						width: 1
					}
				}
			},
			yAxis: {
				type: "value",
				scale: true,
				show: true,
				position: "right",
				splitNumber: 5,
				boundaryGap: [0.01, 0.01],
				axisLine: {
					show: false
				},
				splitLine: {
					show: true,
					lineStyle: {
						color: "rgb(62,72,85)",
						type: "dotted",
						width: 1
					}
				},
				axisLabel: {
					textStyle: {
						color: "rgb(155,155,155)"
					}
				}
			}
		}, true);

		function getcycle() {
			var orderinv = JSUtils.object.strToJsonObj(localStorage.getItem("orderinv"));
			var account = JSUtils.object.strToJsonObj(localStorage.getItem("account"));
			var paras = JSUtils.object.strToJsonObj(localStorage.getItem("paras"));
			var markets = JSUtils.object.strToJsonObj(localStorage.getItem("markets"));
			if (orderinv) {
				var urlitemid = $stateParams.itemID;
				if (urlitemid.substr(-4) == "moni") {
					targetid = "btc_cny";
					type = "V";
					$scope.trade_name = "比特币模拟";
					trade.update_wx_title("比特币模拟交易");
					$scope.btnname = "实盘交易";
					$scope.acconttxt = "模拟币";
					document.getElementsByClassName("abalog_trade_header_btn")[0].style.background = "#d63344"
				} else {
					targetid = $stateParams.itemID.substr(1);
					type = "R";
					$scope.btnname = "充值";
					$scope.acconttxt = "账户余额";
					document.getElementsByClassName("abalog_trade_header_btn")[0].style.background = "#262834";
					for (var n = 0; n < markets.length; n++) {
						if (markets[n]["id"] == targetid && markets[n]["isclose"] == "1") {
							document.getElementById("btn_1").style.visibility = "hidden";
							document.getElementById("btn_2").style.visibility = "hidden"
						}
					}
					var changenameid = JSUtils.object.strToJsonObj(localStorage.getItem("targetinfos"));
					for (var j = 0; j < changenameid.length; j++) {
						if (changenameid[j]["id"] == targetid) {
							$scope.trade_name = changenameid[j]["name"];
							break
						}
					}
					trade.update_wx_title($scope.trade_name + "交易");
					var temp = account;
					var moneytol = 0;
					for (var i = 0; i < temp.length; i++) {
						if (temp[i]["atype"] != "V") {
							moneytol += Number(temp[i]["abalance"])
						}
					}
					$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(moneytol).join(".")
				}
				order_num_limit_min = paras.order_num_limit_min;
				$scope.orderMoney = order_num_limit_min;
				order_num_limit_max = paras.order_num_limit_max;
				for (var i = order_num_limit_min; i <= order_num_limit_max; i++) {
					$scope.moneytol.push(i)
				}
				everyMoney = paras.order_stake_price;
				var data = orderinv;
				for (var i = 0; i < data.length; i++) {
					if (targetid == data[i]["vtargetid"]) {
						$scope.vidarr.push(data[i]["vid"]);
						$scope.vinvarr.push(data[i]["vinv"]);
						$scope.vratearr.push(data[i]["vrate"])
					}
				}
				$scope.vid = $scope.vidarr[0];
				$scope.vinv = $scope.vinvarr[0];
				$scope.vrate = $scope.vratearr[0] * 100;
				$scope.getMoney = JSUtils.number.moneySplitIntAndFloat(Number($scope.orderMoney) * Number($scope.vrate) / 100 * everyMoney).join(".")
			}
		}
		getcycle();
		trade.InitSingleTarget(type, 1, function(data, stateurlstr, error) {
			if (error.errcode == 2000) {
				var temp = data.data["account"];
				var moneytol = 0;
				if (type == "V") {
					for (var i = 0; i < temp.length; i++) {
						if (temp[i]["atype"] == "V") {
							moneytol += Number(temp[i]["abalance"])
						}
					}
				} else {
					if (type == "R" || type == "C") {
						if (temp.length > 0) {
							for (var i = 0; i < temp.length; i++) {
								if (temp[i]["atype"] == "R" || temp[i]["atype"] == "C") {
									moneytol += Number(temp[i]["abalance"])
								}
							}
						} else {
							moneytol = "00.00"
						}
					}
				}
				$scope.accountMoney = JSUtils.number.moneySplitIntAndFloat(moneytol).join(".");
				singleT()
			}
		}, null);
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		if (window.location.href.split("/trad").length > 1) {
			$scope.sycleCtrl = false;
			$ionicTabsDelegate.showBar(true)
		} else {
			var hrefurl = window.location.href;
			if (hrefurl.split("/login").length < 1 || hrefurl.indexOf("/:btc_cny") != -1) {
				window.location.reload()
			}
			$ionicTabsDelegate.showBar(false)
		}
	})
}]);
controllers.controller("freeMoneyContrl", ["$scope", "$ionicTabsDelegate", "trade", function($scope, $ionicTabsDelegate, trade) {
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.coupon = [];
	$scope.abalance = "";
	$scope.bpicurl = "";
	var couponList = [];
	trade.couponsQuery(function(data, state, err, srvJson) {
		if (err.errcode == "2000") {
			if (srvJson.banner) {
				$scope.bpicurl = srvJson.banner.bpicurl
			}
			redmoneylist(data.data.coupons)
		}
	}, function(data, state) {});

	function redmoneylist(temp) {
		if (!temp) {
			return
		}
		couponList = temp;
		$scope.coupon = [];
		for (var i = 0; i < temp.length; i++) {
			var tempt = temp[i];
			var temparr = {};
			temparr.id = tempt.id;
			if (tempt.ctype == 1) {
				temparr.ctype = "单次领取"
			} else {
				if (tempt.ctype == 2) {
					temparr.ctype = "可多次领取"
				}
			}
			temparr.desc = tempt.desc;
			temparr.amount = JSUtils.number.oneFloat(tempt.amount).join(".");
			temparr.e_limit = tempt.e_limit;
			temparr.e_count = tempt.e_count;
			temparr.get_flag = tempt.get_flag;
			temparr.name = tempt.name;
			if (temparr.get_flag == "1") {
				$scope.coupon.push(temparr)
			} else {
				$scope.coupon.unshift(temparr)
			}
		}
	}
	$scope.redmoneyalert = function(id, get_flag) {
		if (get_flag == 0) {
			trade.getCoupon(id, function(data, state) {
				if (data.error.errcode == "2000") {
					trade.alert("领取红包成功");
					redmoneylist(data.data.coupons)
				}
			}, null)
		} else {
			if (get_flag == 1) {} else {
				if (get_flag == 2) {
					trade.alert("您还不满足领取条件")
				}
			}
		}
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("红包活动");
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("rangeContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.selectIndex = 0;
	$scope.selectIndex_range = 3;
	$scope.dtlTrades = [];
	$scope.dtlDCInfos = [];
	$scope.dayRange = [];
	$scope.weekRange = [];
	$scope.monthRange = [];

	function rangeDetial() {
		trade.dtlTopListsQuery("D", function(data, state) {
			if (data.error.errcode == "2000") {
				var arr = data.data.toplists;
				if (arr) {
					for (var i = 0; i < arr.length; i++) {
						var temp = {};
						temp.lorder = arr[i].lorder;
						temp.ltext01 = arr[i].ltext01;
						temp.ltext02 = arr[i].ltext02;
						temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
						temp.ldata02 = arr[i].ldata02;
						$scope.dayRange.push(temp)
					}
				}
			} else {}
		}, null);
		trade.dtlTopListsQuery("W", function(data, state) {
			if (data.error.errcode == "2000") {
				var arr = data.data.toplists;
				if (arr) {
					for (var i = 0; i < arr.length; i++) {
						var temp = {};
						temp.lorder = arr[i].lorder;
						temp.ltext01 = arr[i].ltext01;
						temp.ltext02 = arr[i].ltext02;
						temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
						temp.ldata02 = arr[i].ldata02;
						$scope.weekRange.push(temp)
					}
				}
			} else {}
		}, null);
		trade.dtlTopListsQuery("M", function(data, state) {
			if (data.error.errcode == "2000") {
				var arr = data.data.toplists;
				if (arr) {
					for (var i = 0; i < arr.length; i++) {
						var temp = {};
						temp.lorder = arr[i].lorder;
						temp.ltext01 = arr[i].ltext01;
						temp.ltext02 = arr[i].ltext02;
						temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
						temp.ldata02 = arr[i].ldata02;
						$scope.monthRange.push(temp)
					}
				}
			} else {}
		}, null)
	}
	$scope.channelChange = function(index) {
		if (index == 0) {
			$scope.selectIndex = Number(index);
			document.getElementsByClassName("tradeDetaile")[0].style.display = "block";
			document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
			document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
		} else {
			if (index == 1) {
				$scope.selectIndex = Number(index);
				document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
				document.getElementsByClassName("moneyDetaile")[0].style.display = "block";
				document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
			} else {
				if (index == 2) {
					$scope.selectIndex = Number(index);
					document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
					document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
					document.getElementsByClassName("rangeDetaile")[0].style.display = "block"
				} else {
					if (index == 3) {
						$scope.selectIndex_range = Number(index);
						document.getElementsByClassName("rangeDetaile_day")[0].style.display = "block";
						document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
						document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
					} else {
						if (index == 4) {
							$scope.selectIndex_range = Number(index);
							document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
							document.getElementsByClassName("rangeDetaile_week")[0].style.display = "block";
							document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
						} else {
							if (index == 5) {
								$scope.selectIndex_range = Number(index);
								document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "block"
							}
						}
					}
				}
			}
		}
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("排行榜");
		rangeDetial();
		$ionicTabsDelegate.showBar(false)
	});
	$scope.$on("$ionicView.leave", function() {
		$ionicTabsDelegate.showBar(true)
	})
}]);
controllers.controller("tradeContrl", ["$scope", "$location", "$ionicPopup", "trade", "$timeout", "$ionicTabsDelegate", function($scope, $location, $ionicPopup, trade, $timeout, $ionicTabsDelegate) {
	$scope.userinfo = {};
	$scope.headimgurl = "";
	$scope.username = "";
	$scope.couponcount = 0;
	$scope.rmbmoneyInt = 0;
	$scope.rmbmoneyFloat = "00";
	$scope.redmoneyInt = 0;
	$scope.redmoneyFloat = "00";
	$scope.usertel = "请绑定账号";
	$scope.tradeLogin = function() {
		if ($scope.usertel != "请绑定账号") {
			return
		}
		$location.path("tabs/login_trade")
	};
	$scope.analogTrade = function(val) {
		if (val == "2") {
			$location.path("tabs/range_trade")
		} else {
			$location.path("tabs/analogTrade_trade/:btc_cnymoni/:2")
		}
	};
	$scope.freeMoney = function(val) {
		if (val == "agent") {
			$location.path("tabs/agent_trade")
		} else {
			$location.path("tabs/freeMoney_trade")
		}
	};
	$scope.jumpMoney = function(val) {
		if (1 == val) {
			$location.path("tabs/rechargemoney_trade")
		} else {
			$location.path("tabs/withdraw_trade")
		}
	};

	function setup(data) {
		var accountList = data.account;
		if (accountList) {
			for (var i = 0; i < accountList.length; i++) {
				var account = accountList[i];
				var splitMoney = JSUtils.number.moneySplitIntAndFloat(account.abalance);
				if (account.atype == "R") {
					$scope.rmbmoneyInt = splitMoney[0];
					$scope.rmbmoneyFloat = splitMoney[1]
				} else {
					if (account.atype == "V") {} else {
						if (account.atype == "C") {
							$scope.redmoneyInt = splitMoney[0];
							$scope.redmoneyFloat = splitMoney[1]
						}
					}
				}
			}
		}
		if (data.userinfo) {
			$scope.headimgurl = data.userinfo["headimgurl"];
			$scope.couponcount = data.userinfo["couponcount"];
			if (data.userinfo["nickname"] == "游客") {
				$scope.usertel = "请绑定账号"
			} else {
				$scope.usertel = "你好，" + data.userinfo["nickname"]
			}
		} else {}
	}
	function alltradeinfos() {
		if ($location.path() == "/tabs/trade") {
			trade.allTargetQuery("1", function(data, state) {
				if (data.error["errcode"] == "2000") {
					localStorage.setItem("markets", JSUtils.object.toJsonStr(data.data["markets"]));
					num(data.data.markets);
					$timeout(alltradeinfos, 1000)
				} else {}
			}, null)
		}
	}
	function num(obj) {
		for (var i = 0; i < obj.length; i++) {
			var idnames = obj[i].id + "s";
			var idnamep = obj[i].id + "p";
			if (!document.getElementById(idnamep)) {
				continue
			}
			if (document.getElementById(idnamep).getAttribute("type") == "1") {
				document.getElementById(idnamep).innerHTML = JSUtils.number.fiveFloat(obj[i]["pricetrade"]).join(".")
			} else {
				document.getElementById(idnamep).innerHTML = JSUtils.number.moneySplitIntAndFloat(obj[i]["pricetrade"]).join(".")
			}
			if (obj[i]["isclose"] == 1) {
				var idnamexs = obj[i].id + "xs";
				idnamep = obj[i].id + "p";
				if (!document.getElementById(idnamexs)) {
					continue
				}
				document.getElementById(idnamexs).innerHTML = obj[i]["opentime"];
				document.getElementById(idnamep).innerHTML = '<span class="stopTrade">休市</span>'
			}
		}
	}
	function write(obj1, obj2) {
		var str = "";
		for (var i = 0; i < obj1.length; i++) {
			str += '<a class="trade_list_box" style="display:block" href="#/tabs/analogTrade_trade/:' + obj1[i]["id"] + "/:" + obj1[i]["type"] + '"><div class="lineBox"><div class="trade_list_box_item"><div class="trade_list_box_item_img"><img src="' + obj1[i]["icourl"] + '"></div><div class="list_left"><div class="list_left_first">';
			if (obj1[i]["exturl"] != "") {
				str += '<span style="position: relative;">' + obj1[i]["name"] + '<div class="hot_list" style="position: absolute; right: -20px;top: 50%;margin-top: -5px;"></div></span>'
			} else {
				str += '<span style="position: relative;">' + obj1[i]["name"] + "</span>"
			}
			str += '</div><p class="list_left_second"  id="' + obj1[i]["id"] + 'xs">国际主流虚拟货币</p></div>';
			if (obj1[i].status == "0") {
				str += '<div class="trade_money_list" style=" ">休市</div></div>'
			} else {
				str += '<div class="trade_money_list" id="' + obj1[i]["id"] + 'p" type="' + obj1[i]["type"] + '"></div></div>'
			}
			str += '<p class="trade_list_box_txt">预判到期价格高于或低于下单价格，您即可获得90%的超额收益。1手10元，最高20手，每手交易1元手续费。</p></div></a>'
		}
		document.getElementById("writehtml").innerHTML = str
	}
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("数字交易");
		var width = document.getElementsByClassName("trade_header")[0].offsetWidth;
		document.getElementsByClassName("trade_header")[0].style.height = Number(width) * 0.45 + "px";
		trade.tradePageInit(function(data, state, err, srvJson) {
			if (data.error["errcode"] == "2000") {
				localStorage.setItem("account", JSUtils.object.toJsonStr(data.data["account"]));
				localStorage.setItem("orderinv", JSUtils.object.toJsonStr(data.data["orderinv"]));
				localStorage.setItem("targettypes", JSUtils.object.toJsonStr(data.data["targettypes"]));
				localStorage.setItem("targetinfos", JSUtils.object.toJsonStr(data.data["targetinfos"]));
				localStorage.setItem("paras", JSUtils.object.toJsonStr(data.data["paras"]));
				localStorage.setItem("markets", JSUtils.object.toJsonStr(data.data["markets"]));
				JSUtils.curApp.sessionObj.put("uflag", data.data["uflag"]);
				setup(srvJson);
				write(data.data["targetinfos"], data.data["markets"]);
				var tradelists = data.data["usertrades"];
				var billidlist = [];
				for (var i = 0; i < tradelists.length; i++) {
					var tradelistsobj = tradelists[i];
					if (tradelistsobj.tnoticeflag == "0") {
						trade.bounced(tradelistsobj);
						billidlist.push(tradelistsobj.billid)
					}
				}
				trade.ViewMarketTrade(billidlist, function(data, status) {}, null)
			} else {}
		}, function(data, state) {});
		alltradeinfos();
		$ionicTabsDelegate.showBar(true)
	});
	$scope.$on("$ionicView.afterEnter", function() {
		var width = document.getElementsByClassName("trade_header")[0].offsetWidth;
		document.getElementsByClassName("trade_header")[0].style.height = Number(width) * 0.45 + "px"
	})
}]);
controllers.controller("tradeDetailContrl", ["$scope", "$location", "trade", "$ionicTabsDelegate", function($scope, $location, trade, $ionicTabsDelegate) {
	$scope.selectIndex = 0;
	$scope.selectIndex_range = 3;
	$scope.dtlTrades = [];
	$scope.dtlDCInfos = [];
	$scope.dayRange = [];
	$scope.weekRange = [];
	$scope.monthRange = [];

	function tradeDetail() {
		trade.dtlTradesQuery(function(data, state) {
			var newarr = data.data.trades;
			var i;
			var targetinfosList = localStorage.getItem("targetinfos");
			var targetInfoMap = {};
			if (targetinfosList) {
				targetinfosList = JSUtils.object.strToJsonObj(targetinfosList);
				for (i = 0; i < targetinfosList.length; i++) {
					var tmpInfo = targetinfosList[i];
					targetInfoMap[tmpInfo.id] = tmpInfo
				}
			}
			$scope.dtlTrades = [];
			var changenameid = JSUtils.object.strToJsonObj(localStorage.getItem("targetinfos"));
			for (i = 0; i < newarr.length; i++) {
				var tmpnewarr = newarr[i];
				var temp = {};
				temp.billid = tmpnewarr.billid;
				temp.targetid = tmpnewarr.targetid;
				temp.interval = tmpnewarr.interval;
				temp.ordertime = JSUtils.date.timeStrToTime(tmpnewarr.ordertime);
				temp.tradetimeexpect = JSUtils.date.timeStrToTime(tmpnewarr.tradetimeexpect);
				temp.tradeprice = tmpnewarr.tradeprice;
				if (tmpnewarr.orderdir == "1") {
					temp.orderdir = "看跌"
				} else {
					if (tmpnewarr.orderdir == "2") {
						temp.orderdir = "看涨"
					} else {
						if (tmpnewarr.orderdir == "0") {
							temp.orderdir = "持平"
						}
					}
				}
				temp.one = false;
				temp.two = false;
				temp.three = false;
				temp.four = false;
				if (tmpnewarr.tprofitflag == 1) {
					temp.one = true
				} else {
					if (tmpnewarr.tprofitflag == 2) {
						temp.two = true
					} else {
						if (tmpnewarr.tprofitflag == 0) {
							temp.three = true
						} else {
							if (tmpnewarr.tprofitflag == 3) {
								temp.four = true
							}
						}
					}
				}
				temp.orderprice = JSUtils.number.formatNumeric(tmpnewarr.orderprice, JSUtils.curApp.constants.tradeInfoFormat[tmpnewarr.targetid]);
				temp.charge = JSUtils.number.formatNumeric(tmpnewarr.charge, 2);
				temp.ordernum = tmpnewarr.ordernum;
				temp.stakesum = JSUtils.number.formatNumeric(tmpnewarr.stakesum, 2);
				temp.profitrate = tmpnewarr.profitrate * 100;
				if (tmpnewarr.traderet == "1") {
					temp.traderet = "交割成功"
				} else {
					if (tmpnewarr.traderet == "2") {
						temp.traderet = "交割回滚"
					} else {
						if (tmpnewarr.traderet == "0") {
							temp.traderet = "未交割"
						}
					}
				}
				temp.tradeprice = JSUtils.number.formatNumeric(tmpnewarr.tradeprice, JSUtils.curApp.constants.tradeInfoFormat[tmpnewarr.targetid]);
				if (tmpnewarr.tprofitflag == "0") {
					temp.tprofitflag = "未交割"
				} else {
					if (tmpnewarr.tprofitflag == "1") {
						temp.tprofitflag = "盈利"
					} else {
						if (tmpnewarr.tprofitflag == "2") {
							temp.tprofitflag = "亏损"
						} else {
							if (tmpnewarr.tprofitflag == "3") {
								temp.tprofitflag = "打平"
							} else {
								if (tmpnewarr.tprofitflag == "4") {
									temp.tprofitflag = "异常"
								}
							}
						}
					}
				}
				temp.tradeprofit = JSUtils.number.formatNumeric(tmpnewarr.tradeprofit, 2);
				if (tmpnewarr.traderet == "0") {
					temp.traderet = "未查看"
				} else {
					if (tmpnewarr.traderet == "1") {
						temp.traderet = "已查看"
					}
				}
				if (tmpnewarr.type == "R") {
					temp.type = "实盘"
				} else {
					if (tmpnewarr.type == "V") {
						temp.type = "模拟"
					}
				}
				for (var j = 0; j < changenameid.length; j++) {
					if (changenameid[j]["targetid"] == tmpnewarr.targetid) {
						temp.showName = changenameid[j]["name"]
					}
				}
				if (JSUtils.object.hasSepProperty(targetInfoMap, tmpnewarr.targetid)) {
					temp.showName = targetInfoMap[tmpnewarr.targetid]["name"]
				}
				$scope.dtlTrades.push(temp)
			}
		}, null)
	}
	function moneyDetail() {
		trade.dtlDCInfosQuery(function(data, state, error, srvJson) {
			$scope.dtlDCInfos = [];
			if (srvJson.dcinfos) {
				for (var i = 0; i < srvJson.dcinfos.length; i++) {
					var tmpInfo = srvJson.dcinfos[i];
					tmpInfo.showName = "";
					if (tmpInfo.dctype == "pay") {
						tmpInfo.showName = "账户充值"
					} else {
						if (tmpInfo.dctype == "cashback") {
							tmpInfo.showName = "账户提现"
						} else {
							if (tmpInfo.dctype == "charge") {
								tmpInfo.showName = "手续费"
							} else {
								if (tmpInfo.dctype == "coupon") {
									tmpInfo.showName = "领取红包"
								} else {
									if (tmpInfo.dctype == "order") {
										tmpInfo.showName = "下单"
									} else {
										if (tmpInfo.dctype == "trade") {
											tmpInfo.showName = "交割"
										}
									}
								}
							}
						}
					}
					tmpInfo.showTime = "";
					if (JSUtils.string.isAvailably(tmpInfo.dctime)) {
						var tmpD = JSUtils.date.timeStrTODate(tmpInfo.dctime);
						tmpInfo.showTime = tmpD.format("yyyy-MM-dd hh:mm:ss")
					}
					tmpInfo.showMoney = "";
					if (tmpInfo.dcflag == "d") {
						tmpInfo.showMoney = "+ " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
					} else {
						if (tmpInfo.dcflag == "c") {
							tmpInfo.showMoney = "- " + JSUtils.number.formatNumeric(tmpInfo.dcamount, 2)
						}
					}
					$scope.dtlDCInfos.push(tmpInfo)
				}
			}
		}, null)
	}
	trade.dtlTopListsQuery("D", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.dayRange.push(temp)
				}
			}
		} else {}
	}, null);
	trade.dtlTopListsQuery("W", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.weekRange.push(temp)
				}
			}
		} else {}
	}, null);
	trade.dtlTopListsQuery("M", function(data, state) {
		if (data.error.errcode == "2000") {
			var arr = data.data.toplists;
			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					var temp = {};
					temp.lorder = arr[i].lorder;
					temp.ltext01 = arr[i].ltext01;
					temp.ltext02 = arr[i].ltext02;
					temp.ldata01 = JSUtils.number.moneySplitIntAndFloat(arr[i].ldata01).join(".");
					temp.ldata02 = arr[i].ldata02;
					$scope.monthRange.push(temp)
				}
			}
		} else {}
	}, null);
	$scope.channelChange = function(index) {
		if (index == 0) {
			$scope.selectIndex = Number(index);
			document.getElementsByClassName("tradeDetaile")[0].style.display = "block";
			document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
			document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
		} else {
			if (index == 1) {
				$scope.selectIndex = Number(index);
				document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
				document.getElementsByClassName("moneyDetaile")[0].style.display = "block";
				document.getElementsByClassName("rangeDetaile")[0].style.display = "none"
			} else {
				if (index == 2) {
					$scope.selectIndex = Number(index);
					document.getElementsByClassName("tradeDetaile")[0].style.display = "none";
					document.getElementsByClassName("moneyDetaile")[0].style.display = "none";
					document.getElementsByClassName("rangeDetaile")[0].style.display = "block"
				} else {
					if (index == 3) {
						$scope.selectIndex_range = Number(index);
						document.getElementsByClassName("rangeDetaile_day")[0].style.display = "block";
						document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
						document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
					} else {
						if (index == 4) {
							$scope.selectIndex_range = Number(index);
							document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
							document.getElementsByClassName("rangeDetaile_week")[0].style.display = "block";
							document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "none"
						} else {
							if (index == 5) {
								$scope.selectIndex_range = Number(index);
								document.getElementsByClassName("rangeDetaile_day")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_week")[0].style.display = "none";
								document.getElementsByClassName("rangeDetaile_mouth")[0].style.display = "block"
							}
						}
					}
				}
			}
		}
	};
	$scope.goBackpro = function() {
		window.history.go(-1)
	};
	$scope.$on("$ionicView.beforeEnter", function() {
		trade.update_wx_title("明细");
		tradeDetail();
		moneyDetail();
		$ionicTabsDelegate.showBar(true)
	})
}]);
appService.service("trade", ["$http", "$ionicPopup", "$ionicLoading", "$location", function($http, $ionicPopup, $ionicLoading, $location) {
	this.tradePageInit = function(successFun, errFun) {
		var actionId = "InitMarket";
		var data = {
			uflag: JSUtils.curApp.sessionObj.get("uflag"),
			udownstream: JSUtils.curApp.sessionObj.get("pid")
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.payPageInit = function(successFun, errFun) {
		var actionId = "InitPay";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.cashBackPageInit = function(successFun, errFun) {
		var actionId = "InitCashBack";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.allTargetQuery = function(horn, successFun, errFun) {
		var actionId = "QueryAllTarget";
		var data = {
			horn: horn
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.singleTargetQuery = function(targetid, timess, horn, successFun, errFun) {
		var actionId = "QuerySingleTarget";
		var data = {
			targetid: targetid,
			timess: timess,
			horn: horn
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.marketTradeQuery = function(targetid, timess, type, horn, successFun, errFun) {
		var actionId = "QueryMarketTrade";
		var data = {
			targetid: targetid,
			timess: timess,
			type: type,
			horn: horn
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.dtlTradesQuery = function(successFun, errFun) {
		var actionId = "QueryDtlTrades";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.dtlDCInfosQuery = function(successFun, errFun) {
		var actionId = "QueryDtlDCInfos";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.dtlTopListsQuery = function(type, successFun, errFun) {
		var actionId = "QueryDtlTopLists";
		var data = {
			type: type
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.couponsQuery = function(successFun, errFun) {
		var actionId = "QueryCoupons";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.getCoupon = function(id, successFun, errFun) {
		var actionId = "GetCoupon";
		var data = {
			id: id
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.userInfoQuery = function(successFun, errFun) {
		var actionId = "QueryUserInfo";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.etPayPara = function(amount, testkey, successFun, errFun) {
		var actionId = "GetPayPara";
		var data = {
			amount: amount,
			testkey: testkey
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.getCashBackPara = function(amount, testkey, successFun, errFun) {
		var actionId = "GetCashBackPara ";
		var data = {
			amount: amount,
			testkey: testkey
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.serNameuSet = function(username, successFun, errFun) {
		var actionId = "SetUserName";
		var data = {
			username: username
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.backFeed = function(feedback, successFun, errFun) {
		var actionId = "FeedBack";
		var data = {
			feedback: feedback
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.feedBackQuery = function(successFun, errFun) {
		var actionId = "QueryFeedBack";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.makeTrade = function(trade, successFun, errFun) {
		var actionId = "MakeTrade";
		var data = {
			trade: trade
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.InitMarketTrade = function(targetid, type, timess, successFun, errFun) {
		var actionId = "InitMarketTrade";
		var data = {
			targetid: targetid,
			type: type,
			timess: timess
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.TargetMarketTrade = function(billid, successFun, errFun) {
		var actionId = "TargetMarketTrade";
		var data = {
			billid: billid
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.ViewMarketTrade = function(billid, successFun, errFun) {
		var actionId = "ViewMarketTrade";
		var data = {
			billid: billid
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.InitSingleTarget = function(type, horn, successFun, errFun) {
		var actionId = "InitSingleTarget";
		var data = {
			type: type,
			horn: horn,
			udownstream: JSUtils.curApp.sessionObj.get("pid")
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.UserLogin = function(phoneNum, pwd, successFun, errFun) {
		var actionId = "UserLogin";
		var data = {
			phoneNum: phoneNum,
			pwd: pwd
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.UserLogout = function(successFun, errFun) {
		var actionId = "UserLogout";
		var data = {};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.RegGetCheckCode = function(phoneNum, successFun, errFun) {
		var actionId = "RegGetCheckCode";
		var data = {
			phoneNum: phoneNum
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.RegSave = function(phoneNum, checkCode, pwd, successFun, errFun) {
		var actionId = "RegSave";
		var data = {
			phoneNum: phoneNum,
			checkCode: checkCode,
			pwd: pwd,
			udownstream: JSUtils.curApp.sessionObj.get("pid")
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.ForgetGetChkCode = function(phoneNum, successFun, errFun) {
		var actionId = "ForgetGetChkCode";
		var data = {
			phoneNum: phoneNum
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.ForgetSave = function(phoneNum, checkCode, pwd, successFun, errFun) {
		var actionId = "ForgetSave";
		var data = {
			phoneNum: phoneNum,
			checkCode: checkCode,
			pwd: pwd
		};
		_base_trade(actionId, data, successFun, errFun)
	};
	this.QueryPayCashResult = function(billId, successFun, errFun) {
		var actionId = "QueryPayCashResult";
		var data = {
			billId: billId
		};
		_base_trade(actionId, data, successFun, errFun)
	};

	function _base_trade(actionId, data, successFun, errFun) {
		var url = "/wxwapsrv.htm";
		if (!JSUtils.object.exist(data)) {
			data = {}
		}
		var param = {
			actionid: actionId,
			tradeid: _getTradeId(),
			user: _getCurUserStr(),
			data: JSUtils.object.toJsonStr(data),
			sign: ""
		};
		_sign(param);
		_http(url, param, successFun, errFun)
	}
	function _http(url, param, successFun, errFun) {
		$http({
			method: "POST",
			url: url,
			params: param
		}).success(function(data, status) {
			var error = data.error;
			var srvJson = data.data;
			if (data.error) {
				var tmpErrObj = data.error;
				if (tmpErrObj.errcode == 1500) {
					login()
				} else {
					if (tmpErrObj.errcode == 1510) {
						window.location.replace("/wuli.html")
					} else {
						if (tmpErrObj.showflag == 1) {
							_alert(tmpErrObj.errmsg)
						} else {
							if (tmpErrObj.showflag == "limit_3000" || tmpErrObj.showflag == "limit_4000") {
								_alert(tmpErrObj.errmsg)
							}
						}
					}
				}
			}
			successFun(data, status, error, srvJson)
		}).error(function(data, status) {
			if (errFun) {
				errFun(data, status)
			}
		})
	}
	function _getTradeId() {
		return (new Date().getTime()).toString()
	}
	function _getCurUser() {
		return {
			sessionid: JSUtils.curApp.sessionObj.get("sessionId")
		}
	}
	function _getCurUserStr() {
		var user = _getCurUser();
		return JSUtils.object.toJsonStr(user)
	}
	function _sign(param) {
		var str = "actionid" + param.actionid + "data" + param.data + "tradeid" + param.tradeid + "user" + param.user;
		param.sign = md5(str)
	}
	this.alert = function(msg, btnName, callback) {
		_alert(msg, btnName, callback)
	};

	function _alert(msg, btnName, callback) {
		if (!JSUtils.string.isAvailably(btnName)) {
			btnName = "知道了"
		}
		var alertPopup = $ionicPopup.alert({
			template: '<p style="text-align: left;padding: 10px;font-size: 1.9rem;line-height: 1.4">' + msg + "</p>",
			okText: btnName,
			okType: "buttonpop"
		});
		if (callback) {
			alertPopup.then(function(res) {
				callback()
			})
		}
	}
	this.confirm = function(title, msg, cancelTxt, okTxt) {
		if (!JSUtils.string.isAvailably(cancelTxt)) {
			cancelTxt = "取消"
		}
		if (!JSUtils.string.isAvailably(okTxt)) {
			okTxt = "确定"
		}
		return $ionicPopup.confirm({
			title: title,
			subTitle: "",
			template: '<p style="text-align: left;padding: 10px;font-size: 1.9rem;line-height: 1.4">' + msg + "</p>",
			templateUrl: "",
			cancelText: cancelTxt,
			cancelType: "",
			okText: okTxt,
			okType: ""
		})
	};
	this.showLoading = function() {
		$ionicLoading.show({
			content: "Loading",
			animation: "fade-in",
			showBackdrop: true,
			maxWidth: 200,
			showDelay: 0
		})
	};
	this.bounced = function(trades) {
		var orderdirStr = "看涨";
		var orderdir = trades.orderdir;
		var ordertime = trades.ordertime;
		var tradetimeexpect = trades.tradetimeexpect;
		var stakesum = JSUtils.number.moneySplitIntAndFloat(trades.stakesum).join(".");
		var tradeprofit = trades.tradeprofit;
		var tprofitflag = trades.tprofitflag;
		var targetid = trades.targetid;
		var targetinfors = JSUtils.object.strToJsonObj(localStorage.getItem("targetinfos"));
		var orderprice = "";
		var tradeprice = "";
		for (var i = 0; i < targetinfors.length; i++) {
			if (targetinfors[i]["id"] == targetid) {
				if (targetinfors[i]["type"] == "1") {
					orderprice = JSUtils.number.fiveFloat(trades.orderprice).join(".");
					tradeprice = JSUtils.number.fiveFloat(trades.tradeprice).join(".")
				} else {
					orderprice = JSUtils.number.moneySplitIntAndFloat(trades.orderprice).join(".");
					tradeprice = JSUtils.number.moneySplitIntAndFloat(trades.tradeprice).join(".")
				}
			}
		}
		var orderdircolor = "";
		if (orderdir == "0") {
			orderdirStr = "持平";
			orderdircolor = "atrade_order_txt_gray"
		} else {
			if (orderdir == "1") {
				orderdirStr = "看跌";
				orderdircolor = "atrade_order_txt_green"
			} else {
				if (orderdir == "2") {
					orderdirStr = "看涨";
					orderdircolor = "atrade_order_txt_red"
				}
			}
		}
		var str = "";
		if (tprofitflag == 1 || trades.tproflag == 1) {
			str = '<span class="atrade_order_txt_red">+</span><span class="atrade_order_txt_red" style="font-size: 3rem;">' + tradeprofit + '</span><span class="atrade_order_txt_red">元</span>'
		} else {
			if (tprofitflag == 2 || trades.tproflag == 2) {
				str = '<span class="atrade_order_txt_green">-</span><span class="atrade_order_txt_green" style="font-size: 3rem;">' + stakesum + '</span><span class="atrade_order_txt_green">元</span>'
			} else {
				if (tprofitflag == 3 || trades.tproflag == 3) {
					str = '<span class="atrade_order_txt_gray">持平</span>'
				}
			}
		}
		$ionicPopup.alert({
			title: "到期结果",
			template: '<div class="atrade_order_result" style="padding: 0;background: transparent;"><div class="atrade_order_first" style="padding: 0;background: transparent;margin: 0;"><div class="atrade_order_top"><div class="atrade_order_top_left"><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">建仓方向</p><p class="atrade_order_top_left_fr"><span class="' + orderdircolor + '">' + orderdirStr + '</span></p></div><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">下单时间</p><p class="atrade_order_top_left_fr"><span style="color: #000;">' + ordertime.substr(8, 2) + ":" + ordertime.substr(10, 2) + ":" + ordertime.substr(12, 2) + '</span></p></div><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">下单价</p><p class="atrade_order_top_left_fr"><span class="' + orderdircolor + '">' + orderprice + '</span></p></div></div><div class="atrade_order_top_right"><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">下单金额</p><p class="atrade_order_top_left_fr"><span style="color: #000;">' + stakesum + '元</span></p></div><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">到期时间</p><p class="atrade_order_top_left_fr"><span style="color: #000;">' + tradetimeexpect.substr(8, 2) + ":" + tradetimeexpect.substr(10, 2) + ":" + tradetimeexpect.substr(12, 2) + '</span></p></div><div class="atrade_order_top_left_f"><p class="atrade_order_top_left_fl">到期价</p><p class="atrade_order_top_left_fr"><span style="color: #000;">' + tradeprice + '</span></p></div></div></div><div class="atrade_order_bottom"><div class="atrade_order_b_result"><p class="atrade_order_b_p_left">到期结果</p><p class="atrade_order_b_p_right">' + str + "</p></div></div></div></div>",
			okText: "知道了",
			okType: "buttonpop"
		})
	};
	this.login = function() {
		login()
	};

	function login() {
		var url = $location.path().split("tabs")[1].split("/:");
		var login_url = "";
		console.log($location.path());
		if ($location.path() == "/tabs/withdraw" || $location.path() == "/tabs/recharge") {
			$location.path("tabs/login_money")
		} else {
			if (url.length == 1) {
				var urlsec = url[0].split("_");
				if (urlsec.length == 1) {
					if (urlsec[0] == "/moneyPack") {
						login_url = "tabs/login_money"
					} else {
						login_url = "tabs/login_" + urlsec[0].split("/")[1]
					}
					console.log(login_url);
					$location.path(login_url)
				} else {
					login_url = "tabs/login_" + urlsec[1];
					$location.path(login_url)
				}
			} else {
				if (url.length > 1) {
					var urlthr = url[0].split("_")[1];
					login_url = "tabs/login_" + urlthr;
					console.log(login_url);
					$location.path(login_url)
				}
			}
		}
	}
	this.hideLoading = function() {
		$ionicLoading.hide()
	};
	this.update_wx_title = function(title) {
		var body = document.getElementsByTagName("body")[0];
		document.title = title;
		var iframe = document.createElement("iframe");
		iframe.setAttribute("src", "/empty.png");
		iframe.addEventListener("load", function() {
			setTimeout(function() {
				document.body.removeChild(iframe)
			}, 0)
		});
		document.body.appendChild(iframe)
	}
}]);