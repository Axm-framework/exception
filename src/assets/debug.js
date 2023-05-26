	var traceReg     = new RegExp("(^|\\s)trace-file(\\s|$)");
	var collapsedReg = new RegExp("(^|\\s)collapsed(\\s|$)");

	var e = document.getElementsByTagName("div");
	for (var j = 0, len = e.length; j < len; j++) {
		if (traceReg.test(e[j].className)) {
			e[j].onclick = function() {
				var trace = this.parentNode.parentNode;
				if (collapsedReg.test(trace.className))
					trace.className = trace.className.replace("collapsed", "expanded");
				else
					trace.className = trace.className.replace("expanded", "collapsed");
			}
		}
	}


	function openTab(evt,cityName) {
		var i, tabcontent,tablinks;
		tabcontent = document.getElementsByClassName("tab-content");
		for(i=0; i < tabcontent.length; i++){
			tabcontent[i].style.display="none";
		}

		tablinks = document.getElementsByClassName("tablinks");
		for(i=0; i < tablinks.length; i++){
			tablinks[i].className = tablinks[i].className.replace("active", "");
		}

		document.getElementById(cityName).style.display = "block";
		evt.currentTarget.className += " active";

	}

	document.getElementById("default").click();
