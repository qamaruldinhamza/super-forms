!function(m){"use strict";m.browser||(m.browser={},m.browser.mozilla=/mozilla/.test(navigator.userAgent.toLowerCase())&&!/webkit/.test(navigator.userAgent.toLowerCase()),m.browser.webkit=/webkit/.test(navigator.userAgent.toLowerCase()),m.browser.opera=/opera/.test(navigator.userAgent.toLowerCase()),m.browser.msie=/msie/.test(navigator.userAgent.toLowerCase()));var t={destroy:function(){return m(this).unbind(".maskMoney"),m.browser.msie&&(this.onpaste=null),this},mask:function(t){return this.each(function(){var e=m(this);return"number"==typeof t&&e.val(t),e.trigger("mask")})},unmasked:function(){return this.map(function(){var n,e=m(this).val()||"0",t=-1!==e.indexOf("-");return m(e.split(/\D/).reverse()).each(function(e,t){if(t)return n=t,!1}),e=(e=e.replace(/\D/g,"")).replace(new RegExp(n+"$"),"."+n),t&&(e="-"+e),parseFloat(e)})},init:function(e){return e=m.extend({prefix:"",suffix:"",affixesStay:!0,thousands:",",decimal:".",precision:2,allowZero:!1,allowNegative:!1},e),this.each(function(){var d,n,g=m(this);function getInputSelection(){var e,t,n,a,r,o=g.get(0),i=0,s=0;return"number"==typeof o.selectionStart&&"number"==typeof o.selectionEnd?(i=o.selectionStart,s=o.selectionEnd):(t=document.selection.createRange())&&t.parentElement()===o&&(a=o.value.length,e=o.value.replace(/\r\n/g,"\n"),(n=o.createTextRange()).moveToBookmark(t.getBookmark()),(r=o.createTextRange()).collapse(!1),-1<n.compareEndPoints("StartToEnd",r)?i=s=a:(i=-n.moveStart("character",-a),i+=e.slice(0,i).split("\n").length-1,-1<n.compareEndPoints("EndToEnd",r)?s=a:(s=-n.moveEnd("character",-a),s+=e.slice(0,s).split("\n").length-1))),{start:i,end:s}}function setSymbol(e){var t="";return-1<e.indexOf("-")&&(e=e.replace("-",""),t="-"),t+d.prefix+e+d.suffix}function maskValue(e){var t,n,a,r=-1<e.indexOf("-")&&d.allowNegative?"-":"",o=e.replace(/[^0-9]/g,""),i=o.slice(0,o.length-d.precision);return""===(i=(i=i.replace(/^0*/g,"")).replace(/\B(?=(\d{3})+(?!\d))/g,d.thousands))&&(i="0"),t=r+i,0<d.precision&&(n=o.slice(o.length-d.precision),a=new Array(d.precision+1-n.length).join(0),t+=d.decimal+a+n),setSymbol(t)}function maskAndPosition(e){var t,a,n=g.val().length;g.val(maskValue(g.val())),t=g.val().length,a=e-=n-t,g.each(function(e,t){if(t.setSelectionRange)t.focus(),t.setSelectionRange(a,a);else if(t.createTextRange){var n=t.createTextRange();n.collapse(!0),n.moveEnd("character",a),n.moveStart("character",a),n.select()}})}function mask(){var e=g.val();0<d.precision&&e.indexOf(d.decimal)<0&&(e+=d.decimal+new Array(d.precision+1).join(0)),g.val(maskValue(e))}function preventDefault(e){e.preventDefault?e.preventDefault():e.returnValue=!1}function keypressEvent(e){var t,n,a,r,o,i,s,l,c,u,v,p,f=(e=e||window.event).which||e.charCode||e.keyCode;return void 0!==f&&(f<48||57<f?45===f?(g.val((p=g.val(),d.allowNegative?""!==p&&"-"===p.charAt(0)?p.replace("-",""):"-"+p:p)),!1):43===f?(g.val(g.val().replace("-","")),!1):(13===f||9===f||(!m.browser.mozilla||37!==f&&39!==f||0!==e.charCode)&&preventDefault(e),!0):(i=!(g.val().length>=g.attr("maxlength")&&0<=g.attr("maxlength")),s=getInputSelection(),l=s.start,c=s.end,u=!(s.start===s.end||!g.val().substring(l,c).match(/\d/)),v="0"===g.val().substring(0,1),(i||u||v)&&(preventDefault(e),t=String.fromCharCode(f),a=(n=getInputSelection()).start,r=n.end,o=g.val(),g.val(o.substring(0,a)+t+o.substring(r,o.length)),maskAndPosition(a+1)),!1))}function cutPasteEvent(){setTimeout(function(){mask()},0)}function getDefaultMask(){return(parseFloat("0")/Math.pow(10,d.precision)).toFixed(d.precision).replace(new RegExp("\\.","g"),d.decimal)}d=m.extend({},e),d=m.extend(d,g.data()),g.unbind(".maskMoney"),g.bind("keypress.maskMoney",keypressEvent),g.bind("keydown.maskMoney",function(e){var t,n,a,r,o,i=(e=e||window.event).which||e.charCode||e.keyCode;return void 0!==i&&(n=(t=getInputSelection()).start,a=t.end,8!==i&&46!==i&&63272!==i||(preventDefault(e),r=g.val(),n===a&&(8===i?""===d.suffix?n-=1:(o=r.split("").reverse().join("").search(/\d/),a=1+(n=r.length-o-1)):a+=1),g.val(r.substring(0,n)+r.substring(a,r.length)),maskAndPosition(n),!1))}),g.bind("blur.maskMoney",function(e){if(m.browser.msie&&keypressEvent(e),""===g.val()||g.val()===setSymbol(getDefaultMask()))d.allowZero?d.affixesStay?g.val(setSymbol(getDefaultMask())):g.val(getDefaultMask()):g.val("");else if(!d.affixesStay){var t=g.val().replace(d.prefix,"").replace(d.suffix,"");g.val(t)}g.val()!==n&&g.change()}),g.bind("cut.maskMoney",cutPasteEvent),g.bind("paste.maskMoney",cutPasteEvent),g.bind("mask.maskMoney",mask)})}};m.fn.maskMoney=function(e){return t[e]?t[e].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof e&&e?void m.error("Method "+e+" does not exist on jQuery.maskMoney"):t.init.apply(this,arguments)}}(window.jQuery||window.Zepto);