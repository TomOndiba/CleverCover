// Redefinition of <myfunction>.bind, very useful to keep the scope and <myelement>.addEventListener not defined in IE.
// Useless because browsers that don't define these methods will not manage canvas and so essential.
if(!Function.prototype.bind)
	Function.prototype.bind = function(binding) {
		return function() {
			this.apply(binding, arguments);
		};
	};
if(!HTMLElement.prototype.addEventListener)
	HTMLElement.prototype.addEventListener = function(eventType, listener) {
		this.attachEvent("on" + eventType, function(e) {
			e = e || window.event;
			listener(e);
		});
	};

// A little helper to create an HTML element fast with a parameters list and the classic $ for getElementById
// @params (type, params)
function c(t, o) {
	var d = document.createElement(t);
	o = o || {};
	for(var k in o)
	if(k === 'attributes')
		for(var a in o[k])
		d.setAttribute(a, o[k][a]);
	else
		d[k] = o[k];
	return d;
}

// @params (id)
function $(i) {
	return document.getElementById(i);
}

// Useful for the callbacks in order to do (callback || noop)() instead of a painful if(callback) { callback(); }
function noop() {
}

// Load a picture from its URL and call a function when it's loaded (with metadata)
// @params (url, callback)
function loadImg(u, cb) {
	var i = new Image();
	i.src = u;
	hide(i);
	i.onload = i.onerror = function(e) {
		cb(e);
		i.parentNode.removeChild(i);
	};
	return i;
}

// A round to n instead of 1, a little min/max to assert bounds of a value and a random [0,m]
// @params (value, round)
function r(v, s) {
	s = s || 1;
	return Math.ceil(v / s) * s;
}

// @params (min, value, max)
function mm(mi, v, ma) {
	if(ma>mi) return Math.max(mi, Math.min(v, ma));
	else return Math.max(ma, Math.min(v, mi));
}

// @params (max bound)
function rd(m) {
	return Math.round(Math.random() * m);
}

// Try to log an id for some times. If already locked, return false, else, lock and return true
// @params (lock id, lock time (in s))
var lock = (function() {
	var l = {};
	return function(i, t) {
		var n = new Date().getTime();
		if(l[i] && l[i] > n)
			return false;
		else
			l[i] = n + t * 1000;
		return true;
	};
})();

// File upload via iframe.
// @params (form, callback)
function fuajax(f, cb) {
	var d = 'fu' + rd(999);
	var e = c('iframe', {
		name : d
	});
	hide(e);
	f.setAttribute('target', d);
	e.onload = function() {
		cb(e.contentWindow.document.body.innerHTML);
		e.parentNode.removeChild(e);
	};
}

function formSubmit(attributes, fields) {
	var form = c('form', {
		attributes: attributes
	});
	var key;
	for(key in fields) {
		form.appendChild(c('input', {
			attributes: {
				type: 'hidden',
				name: key,
				value: fields[key]
			}
		}));
	};
	hide(form).submit();
}

// Get canvas context
// @params (canvas id)
function ctx(id) {
	return $(id).getContext('2d');
}

window.URL_PATTERN = '^([^:/?#]+:)//[^/?#]+[^?#]*[^#]*(#.*)?';

// Get max value in an array
Array.prototype.max = function() {
	var max = this[0];
	var len = this.length;

	for(var i = 1; i < len; i++)
	if(this[i] > max)
		max = this[i];

	return max;
};

// Get min value in an array
Array.prototype.min = function() {
	var min = this[0];
	var len = this.length;

	for(var i = 1; i < len; i++)
	if(this[i] < min)
		min = this[i];

	return min;
};

// Hide an element in a hidden div
// @param (element)
function hide(element) {
	if(!$('hidden')) {
		var div = c('div', {
			attributes: {
				style: 'width: 0 !important; height: 0 !important; position: absolute !important; top: -10000px !important;',
				id: 'hidden'
			}
		});
		document.body.appendChild(div);
	}
	$('hidden').appendChild(isElement(element) ? element : element[0]);
	return element;
}

// Asynchronously load JS
function asyncjs(url, id, cb) {
	if(id && $(id)) return;

	var s=c('script');
	s.type='text/javascript';
	s.async=true;
	s.src=url;
	s.id=id;
	s.onload=cb;

	var s2=document.getElementsByTagName('script')[0];
	s2.parentNode.insertBefore(s,s2);
}

function exportNode(node) {
	return node.outerHTML || (
		function(n){
			var div = document.createElement('div'), h;
			div.appendChild( n.cloneNode(true) );
			h = div.innerHTML;
			div = null;
			return h;
		})(node);
}

jQuery.download = function(url, data, method) {
	if (url && data) { 
		data = (typeof data == 'string') ? data : jQuery.param(data);

		var inputs = '';
		jQuery.each(data.split('&'), function() { 
			var pair = this.split('=');
			inputs += '<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});

		var iframe = hide(jQuery('<iframe>').identify());
		hide(jQuery('<form action="'+ url +'" target="'+ iframe.attr('id') +'" method="'+ (method||'post') +'">'+inputs+'</form>')).submit().remove();
	}
};

function isElement(obj) {
  try {
    //Using W3 DOM2 (works for FF, Opera and Chrom)
    return obj instanceof HTMLElement;
  }
  catch(e){
    //Browsers not supporting W3 DOM2 don't have HTMLElement and
    //an exception is thrown and we end up here. Testing some
    //properties that all elements have. (works on IE7)
    return (typeof obj==="object") &&
      (obj.nodeType===1) && (typeof obj.style === "object") &&
      (typeof obj.ownerDocument ==="object");
  }
}

function getImageWeight(dataUrl) {
  return atob(dataUrl.substr(dataUrl.indexOf('base64,') + 7)).length;
}
