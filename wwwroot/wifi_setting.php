<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/xml.js"></script>
	<script src="js/ws_client.js"></script>
	<script type="text/javascript">

		function on_error_report(xml) {
			var err = "";
			var x = xml.getElementsByTagName("Error");
			if (x.length > 0)
				err = "Error: " + x[0].childNodes[0].nodeValue;
			if (err != "")
				document.getElementById("result").innerHTML = err;
		}

		function on_get_wifi_setting(xml) {
			var x;
			var val = "";
			var txt = "";

			val = "No";
			x = xml.getElementsByTagName("Use");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			if (val == 'Yes')
				document.getElementById("use_wifi").checked = true;
			else
				document.getElementById("use_wifi").checked = false;

			val = "";
			x = xml.getElementsByTagName("SSID");
			if (x.length > 0 && x[0].childNodes.length)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("ssid").value = val;

			val = "";
			x = xml.getElementsByTagName("Key");
			if (x.length > 0 && x[0].childNodes.length)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("key").value = val;

			val = "No";
			x = xml.getElementsByTagName("DHCP");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			if (val == 'Yes')
				document.getElementById("use_dhcp").checked = true;
			else
				document.getElementById("use_dhcp").checked = false;

			val = "";
			x = xml.getElementsByTagName("IP");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("ip").value = val;

			val = "";
			x = xml.getElementsByTagName("Subnet");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("subnet_mask").value = val;

			val = "";
			x = xml.getElementsByTagName("DefaultGateway");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("gateway").value = val;

			val = "";
			x = xml.getElementsByTagName("Port");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("port").value = val;

			val = "";
			x = xml.getElementsByTagName("IP_from_dhcp");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("ip_dhcp").value = val;

			val = "";
			x = xml.getElementsByTagName("Subnet_from_dhcp");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("subnet_mask_dhcp").value = val;

			val = "";
			x = xml.getElementsByTagName("DefaultGateway_from_dhcp");
			if (x.length > 0)
				val = x[0].childNodes[0].nodeValue;
			document.getElementById("gateway_dhcp").value = val;

			txt = "Get WiFiSetting OK";
			set_result(txt);
		}

		function get_wifi_setting() {
			var doc = document.implementation.createDocument("", "", null);
			
			var messageElem = doc.createElement("Message");
			var requestElem = doc.createElement("Request");
			requestElem.innerHTML = "GetWiFiSetting";
			messageElem.appendChild(requestElem);
			
			send_relay_message(doc, document.getElementById("session").value, messageElem);
			set_result("");
		}

		function on_set_wifi_setting(xml) {
			var x;
			var ret = "";

			x = xml.getElementsByTagName("Result");
			if (x.length > 0)
				txt = "Result = " + x[0].childNodes[0].nodeValue;

			set_result(txt);
		}

		function set_wifi_setting() {
			var doc = document.implementation.createDocument("", "", null);
			
			var messageElem = doc.createElement("Message");
			var requestElem = doc.createElement("Request");
			requestElem.innerHTML = "SetWiFi";
			messageElem.appendChild(requestElem);

			var paramUse = doc.createElement("Use");
			if (document.getElementById("use_wifi").checked)
				paramUse.innerHTML = "Yes";
			else
				paramUse.innerHTML = "No";
			messageElem.appendChild(paramUse);

			var paramSSID = doc.createElement("SSID");
			paramSSID.innerHTML = document.getElementById("ssid").value;
			messageElem.appendChild(paramSSID);

			var paramKey = doc.createElement("Key");
			paramKey.innerHTML = document.getElementById("key").value;
			messageElem.appendChild(paramKey);

			var paramDHCP = doc.createElement("DHCP");
			if (document.getElementById("use_dhcp").checked)
				paramDHCP.innerHTML = "Yes";
			else
				paramDHCP.innerHTML = "No";
			messageElem.appendChild(paramDHCP);

			var paramIP = doc.createElement("IP");
			paramIP.innerHTML = document.getElementById("ip").value;
			messageElem.appendChild(paramIP);

			var paramSubnet = doc.createElement("Subnet");
			paramSubnet.innerHTML = document.getElementById("subnet_mask").value;
			messageElem.appendChild(paramSubnet);

			var paramGateway = doc.createElement("DefaultGateway");
			paramGateway.innerHTML = document.getElementById("gateway").value;
			messageElem.appendChild(paramGateway);

			var paramPort = doc.createElement("Port");
			paramPort.innerHTML = document.getElementById("port").value;
			messageElem.appendChild(paramPort);

			send_relay_message(doc, document.getElementById("session").value, messageElem);
			set_result("");
		}

		function init() {
			var port = <?php include '../config.inc.php'; echo $Port; ?>;
			var use_wss = <?php include '../config.inc.php'; echo $Use_WSS; ?>;
			ws_init(port, use_wss);

			// Set event handlers.
			ws.onmessage = function(e) {
				// e.data contains received string.
				var xml = parseXml (e.data);
				
				var res = "";
				if (xml.getElementsByTagName("Response").length > 0)
					res = xml.getElementsByTagName("Response")[0].childNodes[0].nodeValue;
				
				if (res == "ErrorReport")
					on_error_report(xml);
				else if (res == "GetWiFiSetting")
					on_get_wifi_setting(xml);
				else if (res == "SetWiFi")
					on_set_wifi_setting(xml);
			};
		}

		function set_result(str) {
			document.getElementById("result").innerHTML = str;
		}

	</script>
</head>

<body onload="init();" onunload="ws_exit();">
	<div id="result" class="result"></div>
	<table>
		<tr>
			<td><button style="width:210px" onclick="get_wifi_setting(); return false;">Get WiFi Setting</button></td>
			<td><button style="width:210px" onclick="set_wifi_setting(); return false;">Set WiFi Setting</button></td>
		</tr>
		<tr>
			<td>Use WiFi:</td>
			<td><input type="checkbox" id='use_wifi'/></td>
		</tr>
		<tr>
			<td>SSID:</td>
			<td><input type="text" id='ssid'/></td>
		</tr>
		<tr>
			<td>Key:</td>
			<td><input type="text" id='key'/></td>
		</tr>
		<tr>
			<td>Use DHCP:</td>
			<td><input type="checkbox" id='use_dhcp'/></td>
		</tr>
		<tr>
			<td>IP:</td>
			<td><input type="text" id='ip'/></td>
		</tr>
		<tr>
			<td>Subnet Mask:</td>
			<td><input type="text" id='subnet_mask'/></td>
		</tr>
		<tr>
			<td>Gateway:</td>
			<td><input type="text" id='gateway'/></td>
		</tr>
		<tr>
			<td>Port:</td>
			<td><input type="text" id='port'/></td>
		</tr>
		<tr>
			<th colspan="2">Received from DHCP(readonly)</th>
		</tr>
		<tr>
			<td>IP:</td>
			<td><input type="text" id='ip_dhcp'/></td>
		</tr>
		<tr>
			<td>Subnet Mask:</td>
			<td><input type="text" id='subnet_mask_dhcp'/></td>
		</tr>
		<tr>
			<td>Gateway:</td>
			<td><input type="text" id='gateway_dhcp'/></td>
		</tr>
	</table>

	<input type="hidden" id='session' name='session' value='<?php echo $_GET["session"]; ?>'></input>
</body>
</html>