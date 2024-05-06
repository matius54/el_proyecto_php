(function(t,r){typeof exports==="object"&&typeof module!=="undefined"?module.exports=r():typeof define==="function"&&define.amd?define(r):(t=typeof globalThis!=="undefined"?globalThis:t||self,t.tinycolor=r())})(this,function(){"use strict";function _typeof(t){"@babel/helpers - typeof";return _typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},_typeof(t)}var n=/^\s+/;var a=/\s+$/;function tinycolor(t,r){t=t?t:"";r=r||{};if(t instanceof tinycolor){return t}if(!(this instanceof tinycolor)){return new tinycolor(t,r)}var e=inputToRGB(t);this._originalInput=t,this._r=e.r,this._g=e.g,this._b=e.b,this._a=e.a,this._roundA=Math.round(100*this._a)/100,this._format=r.format||e.format;this._gradientType=r.gradientType;if(this._r<1)this._r=Math.round(this._r);if(this._g<1)this._g=Math.round(this._g);if(this._b<1)this._b=Math.round(this._b);this._ok=e.ok}tinycolor.prototype={isDark:function isDark(){return this.getBrightness()<128},isLight:function isLight(){return!this.isDark()},isValid:function isValid(){return this._ok},getOriginalInput:function getOriginalInput(){return this._originalInput},getFormat:function getFormat(){return this._format},getAlpha:function getAlpha(){return this._a},getBrightness:function getBrightness(){var t=this.toRgb();return(t.r*299+t.g*587+t.b*114)/1e3},getLuminance:function getLuminance(){var t=this.toRgb();var r,e,n,a,i,o;r=t.r/255;e=t.g/255;n=t.b/255;if(r<=.03928)a=r/12.92;else a=Math.pow((r+.055)/1.055,2.4);if(e<=.03928)i=e/12.92;else i=Math.pow((e+.055)/1.055,2.4);if(n<=.03928)o=n/12.92;else o=Math.pow((n+.055)/1.055,2.4);return.2126*a+.7152*i+.0722*o},setAlpha:function setAlpha(t){this._a=boundAlpha(t);this._roundA=Math.round(100*this._a)/100;return this},toHsv:function toHsv(){var t=rgbToHsv(this._r,this._g,this._b);return{h:t.h*360,s:t.s,v:t.v,a:this._a}},toHsvString:function toHsvString(){var t=rgbToHsv(this._r,this._g,this._b);var r=Math.round(t.h*360),e=Math.round(t.s*100),n=Math.round(t.v*100);return this._a==1?"hsv("+r+", "+e+"%, "+n+"%)":"hsva("+r+", "+e+"%, "+n+"%, "+this._roundA+")"},toHsl:function toHsl(){var t=rgbToHsl(this._r,this._g,this._b);return{h:t.h*360,s:t.s,l:t.l,a:this._a}},toHslString:function toHslString(){var t=rgbToHsl(this._r,this._g,this._b);var r=Math.round(t.h*360),e=Math.round(t.s*100),n=Math.round(t.l*100);return this._a==1?"hsl("+r+", "+e+"%, "+n+"%)":"hsla("+r+", "+e+"%, "+n+"%, "+this._roundA+")"},toHex:function toHex(t){return rgbToHex(this._r,this._g,this._b,t)},toHexString:function toHexString(t){return"#"+this.toHex(t)},toHex8:function toHex8(t){return rgbaToHex(this._r,this._g,this._b,this._a,t)},toHex8String:function toHex8String(t){return"#"+this.toHex8(t)},toRgb:function toRgb(){return{r:Math.round(this._r),g:Math.round(this._g),b:Math.round(this._b),a:this._a}},toRgbString:function toRgbString(){return this._a==1?"rgb("+Math.round(this._r)+", "+Math.round(this._g)+", "+Math.round(this._b)+")":"rgba("+Math.round(this._r)+", "+Math.round(this._g)+", "+Math.round(this._b)+", "+this._roundA+")"},toPercentageRgb:function toPercentageRgb(){return{r:Math.round(bound01(this._r,255)*100)+"%",g:Math.round(bound01(this._g,255)*100)+"%",b:Math.round(bound01(this._b,255)*100)+"%",a:this._a}},toPercentageRgbString:function toPercentageRgbString(){return this._a==1?"rgb("+Math.round(bound01(this._r,255)*100)+"%, "+Math.round(bound01(this._g,255)*100)+"%, "+Math.round(bound01(this._b,255)*100)+"%)":"rgba("+Math.round(bound01(this._r,255)*100)+"%, "+Math.round(bound01(this._g,255)*100)+"%, "+Math.round(bound01(this._b,255)*100)+"%, "+this._roundA+")"},toName:function toName(){if(this._a===0){return"transparent"}if(this._a<1){return false}return t[rgbToHex(this._r,this._g,this._b,true)]||false},toFilter:function toFilter(t){var r="#"+rgbaToArgbHex(this._r,this._g,this._b,this._a);var e=r;var n=this._gradientType?"GradientType = 1, ":"";if(t){var a=tinycolor(t);e="#"+rgbaToArgbHex(a._r,a._g,a._b,a._a)}return"progid:DXImageTransform.Microsoft.gradient("+n+"startColorstr="+r+",endColorstr="+e+")"},toString:function toString(t){var r=!!t;t=t||this._format;var e=false;var n=this._a<1&&this._a>=0;var a=!r&&n&&(t==="hex"||t==="hex6"||t==="hex3"||t==="hex4"||t==="hex8"||t==="name");if(a){if(t==="name"&&this._a===0){return this.toName()}return this.toRgbString()}if(t==="rgb"){e=this.toRgbString()}if(t==="prgb"){e=this.toPercentageRgbString()}if(t==="hex"||t==="hex6"){e=this.toHexString()}if(t==="hex3"){e=this.toHexString(true)}if(t==="hex4"){e=this.toHex8String(true)}if(t==="hex8"){e=this.toHex8String()}if(t==="name"){e=this.toName()}if(t==="hsl"){e=this.toHslString()}if(t==="hsv"){e=this.toHsvString()}return e||this.toHexString()},clone:function clone(){return tinycolor(this.toString())},_applyModification:function _applyModification(t,r){var e=t.apply(null,[this].concat([].slice.call(r)));this._r=e._r;this._g=e._g;this._b=e._b;this.setAlpha(e._a);return this},lighten:function lighten(){return this._applyModification(_lighten,arguments)},brighten:function brighten(){return this._applyModification(_brighten,arguments)},darken:function darken(){return this._applyModification(_darken,arguments)},desaturate:function desaturate(){return this._applyModification(_desaturate,arguments)},saturate:function saturate(){return this._applyModification(_saturate,arguments)},greyscale:function greyscale(){return this._applyModification(_greyscale,arguments)},spin:function spin(){return this._applyModification(_spin,arguments)},_applyCombination:function _applyCombination(t,r){return t.apply(null,[this].concat([].slice.call(r)))},analogous:function analogous(){return this._applyCombination(_analogous,arguments)},complement:function complement(){return this._applyCombination(_complement,arguments)},monochromatic:function monochromatic(){return this._applyCombination(_monochromatic,arguments)},splitcomplement:function splitcomplement(){return this._applyCombination(_splitcomplement,arguments)},triad:function triad(){return this._applyCombination(polyad,[3])},tetrad:function tetrad(){return this._applyCombination(polyad,[4])}};tinycolor.fromRatio=function(t,r){if(_typeof(t)=="object"){var e={};for(var n in t){if(t.hasOwnProperty(n)){if(n==="a"){e[n]=t[n]}else{e[n]=convertToPercentage(t[n])}}}t=e}return tinycolor(t,r)};function inputToRGB(t){var r={r:0,g:0,b:0};var e=1;var n=null;var a=null;var i=null;var o=false;var s=false;if(typeof t=="string"){t=stringInputToObject(t)}if(_typeof(t)=="object"){if(isValidCSSUnit(t.r)&&isValidCSSUnit(t.g)&&isValidCSSUnit(t.b)){r=rgbToRgb(t.r,t.g,t.b);o=true;s=String(t.r).substr(-1)==="%"?"prgb":"rgb"}else if(isValidCSSUnit(t.h)&&isValidCSSUnit(t.s)&&isValidCSSUnit(t.v)){n=convertToPercentage(t.s);a=convertToPercentage(t.v);r=hsvToRgb(t.h,n,a);o=true;s="hsv"}else if(isValidCSSUnit(t.h)&&isValidCSSUnit(t.s)&&isValidCSSUnit(t.l)){n=convertToPercentage(t.s);i=convertToPercentage(t.l);r=hslToRgb(t.h,n,i);o=true;s="hsl"}if(t.hasOwnProperty("a")){e=t.a}}e=boundAlpha(e);return{ok:o,format:t.format||s,r:Math.min(255,Math.max(r.r,0)),g:Math.min(255,Math.max(r.g,0)),b:Math.min(255,Math.max(r.b,0)),a:e}}function rgbToRgb(t,r,e){return{r:bound01(t,255)*255,g:bound01(r,255)*255,b:bound01(e,255)*255}}function rgbToHsl(t,r,e){t=bound01(t,255);r=bound01(r,255);e=bound01(e,255);var n=Math.max(t,r,e),a=Math.min(t,r,e);var i,o,s=(n+a)/2;if(n==a){i=o=0}else{var f=n-a;o=s>.5?f/(2-n-a):f/(n+a);switch(n){case t:i=(r-e)/f+(r<e?6:0);break;case r:i=(e-t)/f+2;break;case e:i=(t-r)/f+4;break}i/=6}return{h:i,s:o,l:s}}function hslToRgb(t,r,e){var n,a,i;t=bound01(t,360);r=bound01(r,100);e=bound01(e,100);function hue2rgb(t,r,e){if(e<0)e+=1;if(e>1)e-=1;if(e<1/6)return t+(r-t)*6*e;if(e<1/2)return r;if(e<2/3)return t+(r-t)*(2/3-e)*6;return t}if(r===0){n=a=i=e}else{var o=e<.5?e*(1+r):e+r-e*r;var s=2*e-o;n=hue2rgb(s,o,t+1/3);a=hue2rgb(s,o,t);i=hue2rgb(s,o,t-1/3)}return{r:n*255,g:a*255,b:i*255}}function rgbToHsv(t,r,e){t=bound01(t,255);r=bound01(r,255);e=bound01(e,255);var n=Math.max(t,r,e),a=Math.min(t,r,e);var i,o,s=n;var f=n-a;o=n===0?0:f/n;if(n==a){i=0}else{switch(n){case t:i=(r-e)/f+(r<e?6:0);break;case r:i=(e-t)/f+2;break;case e:i=(t-r)/f+4;break}i/=6}return{h:i,s:o,v:s}}function hsvToRgb(t,r,e){t=bound01(t,360)*6;r=bound01(r,100);e=bound01(e,100);var n=Math.floor(t),a=t-n,i=e*(1-r),o=e*(1-a*r),s=e*(1-(1-a)*r),f=n%6,l=[e,o,i,i,s,e][f],u=[s,e,e,o,i,i][f],h=[i,i,s,e,e,o][f];return{r:l*255,g:u*255,b:h*255}}function rgbToHex(t,r,e,n){var a=[pad2(Math.round(t).toString(16)),pad2(Math.round(r).toString(16)),pad2(Math.round(e).toString(16))];if(n&&a[0].charAt(0)==a[0].charAt(1)&&a[1].charAt(0)==a[1].charAt(1)&&a[2].charAt(0)==a[2].charAt(1)){return a[0].charAt(0)+a[1].charAt(0)+a[2].charAt(0)}return a.join("")}function rgbaToHex(t,r,e,n,a){var i=[pad2(Math.round(t).toString(16)),pad2(Math.round(r).toString(16)),pad2(Math.round(e).toString(16)),pad2(convertDecimalToHex(n))];if(a&&i[0].charAt(0)==i[0].charAt(1)&&i[1].charAt(0)==i[1].charAt(1)&&i[2].charAt(0)==i[2].charAt(1)&&i[3].charAt(0)==i[3].charAt(1)){return i[0].charAt(0)+i[1].charAt(0)+i[2].charAt(0)+i[3].charAt(0)}return i.join("")}function rgbaToArgbHex(t,r,e,n){var a=[pad2(convertDecimalToHex(n)),pad2(Math.round(t).toString(16)),pad2(Math.round(r).toString(16)),pad2(Math.round(e).toString(16))];return a.join("")}tinycolor.equals=function(t,r){if(!t||!r)return false;return tinycolor(t).toRgbString()==tinycolor(r).toRgbString()};tinycolor.random=function(){return tinycolor.fromRatio({r:Math.random(),g:Math.random(),b:Math.random()})};function _desaturate(t,r){r=r===0?0:r||10;var e=tinycolor(t).toHsl();e.s-=r/100;e.s=clamp01(e.s);return tinycolor(e)}function _saturate(t,r){r=r===0?0:r||10;var e=tinycolor(t).toHsl();e.s+=r/100;e.s=clamp01(e.s);return tinycolor(e)}function _greyscale(t){return tinycolor(t).desaturate(100)}function _lighten(t,r){r=r===0?0:r||10;var e=tinycolor(t).toHsl();e.l+=r/100;e.l=clamp01(e.l);return tinycolor(e)}function _brighten(t,r){r=r===0?0:r||10;var e=tinycolor(t).toRgb();e.r=Math.max(0,Math.min(255,e.r-Math.round(255*-(r/100))));e.g=Math.max(0,Math.min(255,e.g-Math.round(255*-(r/100))));e.b=Math.max(0,Math.min(255,e.b-Math.round(255*-(r/100))));return tinycolor(e)}function _darken(t,r){r=r===0?0:r||10;var e=tinycolor(t).toHsl();e.l-=r/100;e.l=clamp01(e.l);return tinycolor(e)}function _spin(t,r){var e=tinycolor(t).toHsl();var n=(e.h+r)%360;e.h=n<0?360+n:n;return tinycolor(e)}function _complement(t){var r=tinycolor(t).toHsl();r.h=(r.h+180)%360;return tinycolor(r)}function polyad(t,r){if(isNaN(r)||r<=0){throw new Error("Argument to polyad must be a positive number")}var e=tinycolor(t).toHsl();var n=[tinycolor(t)];var a=360/r;for(var i=1;i<r;i++){n.push(tinycolor({h:(e.h+i*a)%360,s:e.s,l:e.l}))}return n}function _splitcomplement(t){var r=tinycolor(t).toHsl();var e=r.h;return[tinycolor(t),tinycolor({h:(e+72)%360,s:r.s,l:r.l}),tinycolor({h:(e+216)%360,s:r.s,l:r.l})]}function _analogous(t,r,e){r=r||6;e=e||30;var n=tinycolor(t).toHsl();var a=360/e;var i=[tinycolor(t)];for(n.h=(n.h-(a*r>>1)+720)%360;--r;){n.h=(n.h+a)%360;i.push(tinycolor(n))}return i}function _monochromatic(t,r){r=r||6;var e=tinycolor(t).toHsv();var n=e.h,a=e.s,i=e.v;var o=[];var s=1/r;while(r--){o.push(tinycolor({h:n,s:a,v:i}));i=(i+s)%1}return o}tinycolor.mix=function(t,r,e){e=e===0?0:e||50;var n=tinycolor(t).toRgb();var a=tinycolor(r).toRgb();var i=e/100;var o={r:(a.r-n.r)*i+n.r,g:(a.g-n.g)*i+n.g,b:(a.b-n.b)*i+n.b,a:(a.a-n.a)*i+n.a};return tinycolor(o)};tinycolor.readability=function(t,r){var e=tinycolor(t);var n=tinycolor(r);return(Math.max(e.getLuminance(),n.getLuminance())+.05)/(Math.min(e.getLuminance(),n.getLuminance())+.05)};tinycolor.isReadable=function(t,r,e){var n=tinycolor.readability(t,r);var a,i;i=false;a=validateWCAG2Parms(e);switch(a.level+a.size){case"AAsmall":case"AAAlarge":i=n>=4.5;break;case"AAlarge":i=n>=3;break;case"AAAsmall":i=n>=7;break}return i};tinycolor.mostReadable=function(t,r,e){var n=null;var a=0;var i;var o,s,f;e=e||{};o=e.includeFallbackColors;s=e.level;f=e.size;for(var l=0;l<r.length;l++){i=tinycolor.readability(t,r[l]);if(i>a){a=i;n=tinycolor(r[l])}}if(tinycolor.isReadable(t,n,{level:s,size:f})||!o){return n}else{e.includeFallbackColors=false;return tinycolor.mostReadable(t,["#fff","#000"],e)}};var i=tinycolor.names={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"0ff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000",blanchedalmond:"ffebcd",blue:"00f",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",burntsienna:"ea7e5d",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"0ff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkgrey:"a9a9a9",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkslategrey:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dimgrey:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"f0f",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",grey:"808080",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgray:"d3d3d3",lightgreen:"90ee90",lightgrey:"d3d3d3",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"789",lightslategrey:"789",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"0f0",limegreen:"32cd32",linen:"faf0e6",magenta:"f0f",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370db",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"db7093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",rebeccapurple:"663399",red:"f00",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",slategrey:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"fff",whitesmoke:"f5f5f5",yellow:"ff0",yellowgreen:"9acd32"};var t=tinycolor.hexNames=flip(i);function flip(t){var r={};for(var e in t){if(t.hasOwnProperty(e)){r[t[e]]=e}}return r}function boundAlpha(t){t=parseFloat(t);if(isNaN(t)||t<0||t>1){t=1}return t}function bound01(t,r){if(isOnePointZero(t))t="100%";var e=isPercentage(t);t=Math.min(r,Math.max(0,parseFloat(t)));if(e){t=parseInt(t*r,10)/100}if(Math.abs(t-r)<1e-6){return 1}return t%r/parseFloat(r)}function clamp01(t){return Math.min(1,Math.max(0,t))}function parseIntFromHex(t){return parseInt(t,16)}function isOnePointZero(t){return typeof t=="string"&&t.indexOf(".")!=-1&&parseFloat(t)===1}function isPercentage(t){return typeof t==="string"&&t.indexOf("%")!=-1}function pad2(t){return t.length==1?"0"+t:""+t}function convertToPercentage(t){if(t<=1){t=t*100+"%"}return t}function convertDecimalToHex(t){return Math.round(parseFloat(t)*255).toString(16)}function convertHexToDecimal(t){return parseIntFromHex(t)/255}var o=function(){var t="[-\\+]?\\d+%?";var r="[-\\+]?\\d*\\.\\d+%?";var e="(?:"+r+")|(?:"+t+")";var n="[\\s|\\(]+("+e+")[,|\\s]+("+e+")[,|\\s]+("+e+")\\s*\\)?";var a="[\\s|\\(]+("+e+")[,|\\s]+("+e+")[,|\\s]+("+e+")[,|\\s]+("+e+")\\s*\\)?";return{CSS_UNIT:new RegExp(e),rgb:new RegExp("rgb"+n),rgba:new RegExp("rgba"+a),hsl:new RegExp("hsl"+n),hsla:new RegExp("hsla"+a),hsv:new RegExp("hsv"+n),hsva:new RegExp("hsva"+a),hex3:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex6:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/,hex4:/^#?([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})([0-9a-fA-F]{1})$/,hex8:/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/}}();function isValidCSSUnit(t){return!!o.CSS_UNIT.exec(t)}function stringInputToObject(t){t=t.replace(n,"").replace(a,"").toLowerCase();var r=false;if(i[t]){t=i[t];r=true}else if(t=="transparent"){return{r:0,g:0,b:0,a:0,format:"name"}}var e;if(e=o.rgb.exec(t)){return{r:e[1],g:e[2],b:e[3]}}if(e=o.rgba.exec(t)){return{r:e[1],g:e[2],b:e[3],a:e[4]}}if(e=o.hsl.exec(t)){return{h:e[1],s:e[2],l:e[3]}}if(e=o.hsla.exec(t)){return{h:e[1],s:e[2],l:e[3],a:e[4]}}if(e=o.hsv.exec(t)){return{h:e[1],s:e[2],v:e[3]}}if(e=o.hsva.exec(t)){return{h:e[1],s:e[2],v:e[3],a:e[4]}}if(e=o.hex8.exec(t)){return{r:parseIntFromHex(e[1]),g:parseIntFromHex(e[2]),b:parseIntFromHex(e[3]),a:convertHexToDecimal(e[4]),format:r?"name":"hex8"}}if(e=o.hex6.exec(t)){return{r:parseIntFromHex(e[1]),g:parseIntFromHex(e[2]),b:parseIntFromHex(e[3]),format:r?"name":"hex"}}if(e=o.hex4.exec(t)){return{r:parseIntFromHex(e[1]+""+e[1]),g:parseIntFromHex(e[2]+""+e[2]),b:parseIntFromHex(e[3]+""+e[3]),a:convertHexToDecimal(e[4]+""+e[4]),format:r?"name":"hex8"}}if(e=o.hex3.exec(t)){return{r:parseIntFromHex(e[1]+""+e[1]),g:parseIntFromHex(e[2]+""+e[2]),b:parseIntFromHex(e[3]+""+e[3]),format:r?"name":"hex"}}return false}function validateWCAG2Parms(t){var r,e;t=t||{level:"AA",size:"small"};r=(t.level||"AA").toUpperCase();e=(t.size||"small").toLowerCase();if(r!=="AA"&&r!=="AAA"){r="AA"}if(e!=="small"&&e!=="large"){e="small"}return{level:r,size:e}}return tinycolor});

class ThemeUI {
    //falta probar mas esto, no se si funciona los temas auto y si los custom over colors se actualizan correctamente
    #localStorageKey = "_themeUI";
    #allowedThemes = ["light","dark","auto","custom"];
    #theme = "auto";
    #isDark;
    #customOverColors = false;
    #icons = {};

    #setDefaultTheme(){
        if(this.#theme !== "auto")return;
        if(window.matchMedia && 
            window.matchMedia('(prefers-color-scheme: dark)').matches){
            this.#theme = this.#allowedThemes[1];
            this.#isDark = true;
        }else{
            this.#theme = this.#allowedThemes[0];
            this.#isDark = false;
        }
    }

    #colors = {
        mainColor:{
            css:"--main-color",
            light:"#FFFFFF",
            dark:"#1d6fb8",
            hex:true,
            rgb:true
        },
        secondColor:{
            css:"--second-color",
            light:"#c8c8ff",
            dark:"#144d80",
            hex:true,
            rgb:true
        },
        overMainColor:{
            css:"--over-main-color",
            light:"#000000",
            dark:"#FFFFFF",
            hex:true,
            rgb:false
        },
        overSecondColor:{
            css:"--over-second-color",
            light:"#000000",
            dark:"#FFFFFF",
            hex:true,
            rgb:false
        },
        backgroudColor:{
            css:"--background-color",
            light:"#d3d3d3",
            dark:"#1c1b22",
            hex:true,
            rgb:false
        }/*,
        errorColor:{
            css:"--error-color",
            hex:true,
            rgb:false
        },
        okColor:{
            css:"--ok-color",
            hex:true,
            hex:false
        }*/
    }
    #extra = {
        blur:{
            default:10,
            css:"--blur-config",
            max:15,
            min:1
        }
    };
    constructor() {
        this.load();
        this.#setDefaultTheme();
        for(let color in this.#colors) this[color] = new tinycolor(this.#colors[color][this.#theme]);
        for(let ex in this.#extra){
            switch(ex){
                case "blur":
                    this[ex] = {
                        enabled: true,
                        value: this.#extra[ex].default
                    }
                break;
            }
        }
        document.querySelectorAll(".icon").forEach(e => this.addIcon(e, e.id));
        this.save();
        this.apply();
        this.addToggleButton();
        window.addEventListener('visibilitychange', () => {
            this.load();
            this.apply();
            this.syncToggleButton();
        });
    }
    getAllPropierties(){
        let propierties = [];
        for(let color in this.#colors)propierties.push(color);
        for(let extra in this.#extra)propierties.push(extra);
        return propierties;
    }
    setValue(key,value){
        if(typeof(key)!=="string") return;
        if(this.#colors[key]){
            let color = new tinycolor(value);
            if(!color.isValid()){   
                console.error(`color '${value}' is NOT valid`);
                return;
            }
            let prefix = "over";
            if(key.startsWith(prefix))this.#customOverColors = true;
            if(!this.#customOverColors && !key.startsWith(prefix)){
                let propierty = prefix.concat(key[0].toUpperCase(),key);
                this[key] = tinycolor.isReadable(this[propierty], "white", {}) ? new tinycolor("white") : tinycolor("black");
            }
            this[key] = color;
            if(this.#theme!=="custom")this.setTheme("custom");
        }
        if(this.#extra[key]){
            switch(key){
                case "blur":
                    if(/^\d+$/.test(value)){
                        let valueInt = typeof(value)==="number"? value : parseInt(value);
                        let min = this.#extra[key].min;
                        let max = this.#extra[key].max;
                        if(valueInt > max){
                            valueInt = max;
                        }else if(valueInt < min){
                            valueInt = min;
                        }
                        this[key].value = valueInt;
                    }else{
                        switch(value){
                            case "toggle":
                                this[key].enabled = !this[key].enabled;
                            break;
                        }
                    }
                break;
            }
        }
    }
    setTheme(theme){
        if(typeof(theme)==="string" && this.#allowedThemes.includes(theme)){
            this.#theme = theme;
            this.save();
            this.apply();
        }
    }
    getValue(input){
        switch(input){
            case "theme":
                return this.#theme;
            break;
        }
    }
    save(){
        let saveColorsObj = {};
        let saveExtraObj = {};
        let key;
        for(key in this.#colors) saveColorsObj[key] = `#${this[key].toHex()}`;
        for(key in this.#extra) saveExtraObj[key] = this[key];
        localStorage.setItem(this.#localStorageKey,JSON.stringify({
            colors: saveColorsObj,
            extra: saveExtraObj,
            theme: this.#theme,
            customOverColors: this.#customOverColors
        }));
    }
    load(){
        let saved = localStorage.getItem(this.#localStorageKey);
        if(!saved) return;
        let savedObj = JSON.parse(saved);
        for(let el in savedObj){
            let element = savedObj[el];
            switch (el){
                case "colors":
                    for(let color in element)
                        this[color] = new tinycolor(element[color]);
                break;
                case "extra":
                    for(let extra in element)this[extra] = element[extra];
                break;
                case "theme":
                    this.#theme = element;
                break;
                case "customOverColors":
                    this.#customOverColors = element;
                break;
            }
        }
    }
    apply(){
        let styleSelector = document.documentElement.style;
        for(let color in this.#colors){
            let cssString = this.#colors[color]["css"];
            let colorSelected;
            switch(this.#theme) {
                case "custom":
                    colorSelected = this[color];
                break;
                default:
                    colorSelected = new tinycolor(this.#colors[color][this.#theme]);
                break;
            }
            if(this.#colors[color]["hex"]){
                styleSelector.setProperty(`${cssString}`,`#${colorSelected.toHex()}`);
                console.debug(`${cssString}: #${colorSelected.toHex()};`)
            }
            if(this.#colors[color]["rgb"]){
                let colorRGB = colorSelected.toRgb();
                ["r","g","b"].forEach((color)=>{
                    styleSelector.setProperty(`${cssString}-${color}`,colorRGB[color].toString());
                    console.debug(`${cssString}-${color}: ${colorRGB[color].toString()};`)
                });
            }
        }
        for(let extra in this.#extra){
            switch(extra){
                case "blur":
                    let cssString = this.#extra[extra].css;
                    let isBlurEnabled = this[extra].enabled;
                    if(typeof(isBlurEnabled)!=="boolean")return;
                    let value = isBlurEnabled? `blur(${this[extra].value}px)` : "unset";
                    styleSelector.setProperty(cssString,value);
                    console.debug(`${cssString}: ${value};`);
                break;
            }
        }
        for(let icon in this.#icons){
            const dark = this.isDark();
            this.#icons[icon].forEach(element => {
                element.setAttribute("src",`icons/${icon}${dark ? "_dark" : ""}.svg`);
            });
        }
    }
    isDark(){
        return this.getValue("theme") === "dark";
    }
    addIcon(element, key){
        element.setAttribute("src",`icons/${key}${this.isDark() ? "_dark" : ""}.svg`);
        if(!this.#icons[key]) this.#icons[key] = [];
        this.#icons[key].push(element);
    }
    removeIcon(element) {
        for (const key in this.#icons) {
            const index = this.#icons[key].indexOf(element);
            if (index !== -1){
                this.#icons[key].splice(index, 1);
                return true;
            }
        }
        return false;
    }
    nextTheme(){
        //TODO: cuando funcionen los temas auto y custom, descomenta esta linea
        //const themes = this.#allowedThemes;
        const themes = ["light","dark"];
        return themes[(themes.indexOf(this.#theme) + 1) % themes.length];
    }
    syncToggleButton(){
        const img = document.querySelector("header.nav img.theme-toggle");
        this.removeIcon(img)
        this.addIcon(img, this.nextTheme());
    }
    addToggleButton(){
        const updateIcon = (a) => {
            const img = a.querySelector("img");
            if(this.removeIcon(img)){
                this.toggleTheme();
            }
            img.id = this.nextTheme();
            this.addIcon(img,img.id);
        };
        const header = document.querySelector("header.nav div.options:last-child");
        if(!header) return;
        const button = document.createElement("a");
        button.title = "Cambiar tema";
        const img = document.createElement("img"); 
        img.classList.add("icon","button","theme-toggle");
        img.setAttribute("src",`icons/dark_dark.svg`);
        button.appendChild(img);
        updateIcon(button);
        button.addEventListener("click",(e)=>updateIcon(e.target.closest("a")));
        header.appendChild(button);
    }
    toggleTheme(){
        const isDark = this.isDark();
        this.setTheme(this.nextTheme());
        return isDark;
    }
}