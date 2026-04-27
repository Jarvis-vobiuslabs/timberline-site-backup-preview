<?php 
	include("process-order.php");

	if($_SERVER['HTTPS'] != "on") {
		$httpsUrl = "https://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		header("Location: $httpsUrl");
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Timberline Lawn and Landscape [Order Service] | 214.557.6975 | Mowing the North Dallas and surrounding areas!</title>
		
		<link rel="stylesheet" type="text/css" href="reset.css" />
		<link rel="stylesheet" type="text/css" href="styles.css" />
        
		
		<style type="text/css">
			#service-details div {
				display: none;
			}
		</style>
		
		<script type="text/javascript">
			function showService(service) {
				document.getElementById("service-details-lcs").style.display = 'none';
				document.getElementById("service-details-lcb").style.display = 'none';
				document.getElementById("service-details-landscape").style.display = 'none';
				document.getElementById(service).style.display = 'block';
			}
			function set_billing(box) 
			{ 
				var f = box.form, b_which = box.checked, from_el, to_el, i = 0;
				var fld_name = new Array('address' , 'city' , 'state' , 'zip');
				while (from_el = f[fld_name[i]])
				{ 
					to_el = f['cc_' + fld_name[i++]];
					to_el.value = b_which ? from_el.value : '';
					
					if (to_el.readOnly != null)	to_el.readOnly = b_which ? true : false;
					else to_el.onfocus = b_which ? function() {this.blur();}
					: null;
				}
			}
		</script>
	</head>
<body> 
<div id="container">
	<div id="navigation">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="dallas-lawn-care.php">Lawn Care</a></li>
			<li><a href="dallas-landscaping.php">Landscaping</a></li>
			<li><a href="order.php" class="selected">Order Service</a></li>
			<li><a href="about.php">About Us</a></li>
		</ul>
    </div>
    
	<div id="header">
    	<h1><img src="http://timberlinelawnandlandscape.com/Home_files/droppedImage.jpg" alt="Timberline Lawn and Landscape" /></h1>
    	<div id="supportingText">
    		<p>No <br />complicated <br />estimates!</p>
    	</div>
    </div>
    <div class="clearer"><!-- --></div>
    <div id="main">
    	<div id="tagline">
    		<h3>Payment made simple! Problems, questions, or concerns call (214)557-6975</h3>
    	</div>
    	
		<div id="signUp">
			<?php
				if($_SESSION['error']) { ?>
					<div class="error">
						<h4>Please correct the following information before continuing:</h4>
						<?=$_SESSION['error']?>
					</div>
					<?php
					unset($_SESSION['error']);
				}
				if($_SESSION['orderDeclined']) { ?>
					<div class="declined">
						<h4>Im sorry, but the order has been declined.</h4>
						<p>Your card has not been charged. You may try again, or call (214) 557-6975 to complete your order over the phone.</p>
					</div>
					<?php
					unset($_SESSION['orderDeclined']);
				}
				if($_SESSION['orderAccepted']) { ?>
					<div class="accepted">
						<h4>Your order has been successfully completed. We look forward to working with you soon!</h4>
					</div>
					<?php
					unset($_SESSION['orderAccepted']);
				}
	        ?>
			<form action="order.php?cmd=submitOrder" method="post" name="paymentForm">
				<table>
		        	<colgroup>
		            	<col class="col1" />
		                <col class="col2" />
		            </colgroup>

<p align="center"<span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=8jU9E2mJniO4BGvMYdJsawyjFBsgtLw6JQ2TqMCFbPFqHqMcL9Wb"></script></span> </p>
		            
		            <tr>
						<td><label for="state">Select your service area:</label></td>
		                <td>
		                	<select name="serviceable">
		                    	<option></option>
								<option value="75248"<? if($_SESSION['serviceable'] == "75248") echo " selected"; ?>>75248</option>
								<option value="75254"<? if($_SESSION['serviceable'] == "75254") echo " selected"; ?>>75254</option>
								<option value="75240"<? if($_SESSION['serviceable'] == "75240") echo " selected"; ?>>75240</option>
								<option value="75230"<? if($_SESSION['serviceable'] == "75230") echo " selected"; ?>>75230</option>
								<option value="75243"<? if($_SESSION['serviceable'] == "75243") echo " selected"; ?>>75243</option>
								<option value="75225"<? if($_SESSION['serviceable'] == "75225") echo " selected"; ?>>75225</option>
								<option value="75231"<? if($_SESSION['serviceable'] == "75231") echo " selected"; ?>>75231</option>
								<option value="75238"<? if($_SESSION['serviceable'] == "75238") echo " selected"; ?>>75238</option>
								<option value="75206"<? if($_SESSION['serviceable'] == "75206") echo " selected"; ?>>75206</option>
								<option value="75214"<? if($_SESSION['serviceable'] == "75214") echo " selected"; ?>>75214</option>
								<option value="75218"<? if($_SESSION['serviceable'] == "75218") echo " selected"; ?>>75218</option>
								<option value="75228"<? if($_SESSION['serviceable'] == "75228") echo " selected"; ?>>75228</option>
								<option value="75204"<? if($_SESSION['serviceable'] == "75204") echo " selected"; ?>>75204</option>
								<option value="75219"<? if($_SESSION['serviceable'] == "75219") echo " selected"; ?>>75219</option>
								<option value="75205"<? if($_SESSION['serviceable'] == "75205") echo " selected"; ?>>75205</option>
								<option value="75041"<? if($_SESSION['serviceable'] == "75041") echo " selected"; ?>>75041</option>
								<option value="75042"<? if($_SESSION['serviceable'] == "75042") echo " selected"; ?>>75042</option>
								<option value="75081"<? if($_SESSION['serviceable'] == "75081") echo " selected"; ?>>75081</option>
								<option value="75080"<? if($_SESSION['serviceable'] == "75080") echo " selected"; ?>>75080</option>
								<option value="75150"<? if($_SESSION['serviceable'] == "75150") echo " selected"; ?>>75150</option>
								<option value="75209"<? if($_SESSION['serviceable'] == "75209") echo " selected"; ?>>75209</option>
								<option value="75220"<? if($_SESSION['serviceable'] == "75220") echo " selected"; ?>>75220</option>
								<option value="75229"<? if($_SESSION['serviceable'] == "75229") echo " selected"; ?>>75229</option>
								<option value="75244"<? if($_SESSION['serviceable'] == "75244") echo " selected"; ?>>75244</option>
							</select>
						</td>
							
					</tr>
		            
		            <tr><td colspan="2"><h4>Customer Information</h4></td></tr>
			            <tr>
			            	<td><label for="firstName">First Name:</label></td>
			                <td><input type="text" size="30" maxlength="32" name="firstName" value="<? echo $_SESSION['firstName']; ?>" /></td>
			            </tr> 
			            <tr>
			            	<td><label for="lastName">Last Name:</label></td>
			                <td><input type="text" size="30" maxlength="32" name="lastName" value="<? echo $_SESSION['lastName']; ?>" /></td>
			            </tr>
			            <tr>
			            	<td><label for="email">Email:</label></td>
			                <td><input type="text" size="30" name="email" value="<? echo $_SESSION['email']; ?>" /></td>
			            </tr> 
			            <tr>
			            	<td><label for="phone">Phone Number:</label></td>
			                <td><input type="text" size="30" name="phone" value="<? echo $_SESSION['phone']; ?>" /></td>
			            </tr>
	
	
	
		            <tr><td colspan="2"><h4>Service Address</h4></td></tr>
			            <tr>
			            	<td><label for="address">Address:</label></td>
			                <td><textarea name="address" rows="2" cols="23"><? echo $_SESSION['address']; ?></textarea></td>
			            </tr> 
			            <tr>
			            	<td><label for="city">City:</label></td>
			                <td><input type="text" size="30" maxlength="80" name="city" value="<? echo $_SESSION['city']; ?>" /></td>
			            </tr>
			            <tr>
			            	<td><label for="state">State:</label></td>
			                <td>
			                	<select name="state">
			                    	<option></option>
			                        <option value="AK"<? if($_SESSION['state'] == "AK") echo " selected"; ?>>AK</option><option value="AL"<? if($_SESSION['state'] == "AL") echo " selected"; ?>>AL</option><option value="AR"<? if($_SESSION['state'] == "AR") echo " selected"; ?>>AR</option><option value="AZ"<? if($_SESSION['state'] == "AZ") echo " selected"; ?>>AZ</option><option value="CA"<? if($_SESSION['state'] == "CA") echo " selected"; ?>>CA</option><option value="CO"<? if($_SESSION['state'] == "CO") echo " selected"; ?>>CO</option><option value="CT"<? if($_SESSION['state'] == "CT") echo " selected"; ?>>CT</option><option value="DC"<? if($_SESSION['state'] == "DC") echo " selected"; ?>>DC</option>
			                        <option value="DE"<? if($_SESSION['state'] == "DE") echo " selected"; ?>>DE</option><option value="FL"<? if($_SESSION['state'] == "FL") echo " selected"; ?>>FL</option><option value="GA"<? if($_SESSION['state'] == "GA") echo " selected"; ?>>GA</option><option value="HI"<? if($_SESSION['state'] == "HI") echo " selected"; ?>>HI</option><option value="IA"<? if($_SESSION['state'] == "IA") echo " selected"; ?>>IA</option><option value="ID"<? if($_SESSION['state'] == "ID") echo " selected"; ?>>ID</option><option value="IL"<? if($_SESSION['state'] == "IL") echo " selected"; ?>>IL</option><option value="IN"<? if($_SESSION['state'] == "IN") echo " selected"; ?>>IN</option>
			                        <option value="KS"<? if($_SESSION['state'] == "KS") echo " selected"; ?>>KS</option><option value="KY"<? if($_SESSION['state'] == "KY") echo " selected"; ?>>KY</option><option value="LA"<? if($_SESSION['state'] == "LA") echo " selected"; ?>>LA</option><option value="MA"<? if($_SESSION['state'] == "MA") echo " selected"; ?>>MA</option><option value="MD"<? if($_SESSION['state'] == "MD") echo " selected"; ?>>MD</option><option value="ME"<? if($_SESSION['state'] == "ME") echo " selected"; ?>>ME</option><option value="MI"<? if($_SESSION['state'] == "MI") echo " selected"; ?>>MI</option><option value="MN"<? if($_SESSION['state'] == "MN") echo " selected"; ?>>MN</option>
									<option value="MO"<? if($_SESSION['state'] == "MO") echo " selected"; ?>>MO</option><option value="MS"<? if($_SESSION['state'] == "MS") echo " selected"; ?>>MS</option><option value="MT"<? if($_SESSION['state'] == "MT") echo " selected"; ?>>MT</option><option value="NC"<? if($_SESSION['state'] == "NC") echo " selected"; ?>>NC</option><option value="ND"<? if($_SESSION['state'] == "ND") echo " selected"; ?>>ND</option><option value="NE"<? if($_SESSION['state'] == "NE") echo " selected"; ?>>NE</option><option value="NH"<? if($_SESSION['state'] == "NH") echo " selected"; ?>>NH</option><option value="NJ"<? if($_SESSION['state'] == "NJ") echo " selected"; ?>>NJ</option>
									<option value="NM"<? if($_SESSION['state'] == "NM") echo " selected"; ?>>NM</option><option value="NV"<? if($_SESSION['state'] == "NV") echo " selected"; ?>>NV</option><option value="NY"<? if($_SESSION['state'] == "NY") echo " selected"; ?>>NY</option><option value="OH"<? if($_SESSION['state'] == "OH") echo " selected"; ?>>OH</option><option value="OK"<? if($_SESSION['state'] == "OK") echo " selected"; ?>>OK</option><option value="OR"<? if($_SESSION['state'] == "OR") echo " selected"; ?>>OR</option><option value="PA"<? if($_SESSION['state'] == "PA") echo " selected"; ?>>PA</option><option value="RI"<? if($_SESSION['state'] == "RI") echo " selected"; ?>>RI</option>
			                        <option value="SC"<? if($_SESSION['state'] == "SC") echo " selected"; ?>>SC</option><option value="SD"<? if($_SESSION['state'] == "SD") echo " selected"; ?>>SD</option><option value="TN"<? if($_SESSION['state'] == "TN") echo " selected"; ?>>TN</option><option value="TX"<? if($_SESSION['state'] == "TX") echo " selected"; ?>>TX</option><option value="UT"<? if($_SESSION['state'] == "UT") echo " selected"; ?>>UT</option><option value="VA"<? if($_SESSION['state'] == "VA") echo " selected"; ?>>VA</option><option value="VT"<? if($_SESSION['state'] == "VT") echo " selected"; ?>>VT</option><option value="WA"<? if($_SESSION['state'] == "WA") echo " selected"; ?>>WA</option>
			                        <option value="WI"<? if($_SESSION['state'] == "WI") echo " selected"; ?>>WI</option><option value="WV"<? if($_SESSION['state'] == "WV") echo " selected"; ?>>WV</option><option value="WY"<? if($_SESSION['state'] == "WY") echo " selected"; ?>>WY</option><option value="AA"<? if($_SESSION['state'] == "AA") echo " selected"; ?>>AA</option><option value="AE"<? if($_SESSION['state'] == "AE") echo " selected"; ?>>AE</option><option value="AP"<? if($_SESSION['state'] == "AP") echo " selected"; ?>>AP</option><option value="AS"<? if($_SESSION['state'] == "AS") echo " selected"; ?>>AS</option><option value="FM"<? if($_SESSION['state'] == "FM") echo " selected"; ?>>FM</option>
									<option value="GU"<? if($_SESSION['state'] == "GU") echo " selected"; ?>>GU</option><option value="MH"<? if($_SESSION['state'] == "MH") echo " selected"; ?>>MH</option><option value="MP"<? if($_SESSION['state'] == "MP") echo " selected"; ?>>MP</option><option value="PR"<? if($_SESSION['state'] == "PR") echo " selected"; ?>>PR</option><option value="PW"<? if($_SESSION['state'] == "PW") echo " selected"; ?>>PW</option><option value="VI"<? if($_SESSION['state'] == "VI") echo " selected"; ?>>VI</option>
								</select>
			                </td>
			            </tr>
			            <tr>
			            	<td><label for="zip">Zip Code:</label></td>
			                <td><input type="text" size="10" maxlength="10" name="zip" value="<? echo $_SESSION['zip']; ?>" /></td>
			            </tr>
	
	
	
		            <tr><td colspan="2"><h4>Service Information</h4></td></tr>
		            	<tr>
		            		<td>
		            			<ul>
									<li><input id="list-services-lcs" type="radio" name="service" value="lawncare-small" onclick="showService('service-details-lcs');" />Lawn Care &lt;20,000 sq. ft.</li>
									<li><input id="list-services-lcb" type="radio" name="service" value="lawncare-big" onclick="showService('service-details-lcb');" />Lawn Care &gt;20,000 sq. ft.</li>
									<li><input id="list-services-ls" type="radio" name="service" value="landscaping" onclick="showService('service-details-landscape');" />Landscaping</li>
								</ul>
							</td>
							<td>
								<div id="service-details">
									<div id="service-details-lcs">
									<h5>Lawn Service Details</h5>
										<p>Service Frequency: 
										<select name="lcs-frequency">
											<option value="weekly"<? if($_SESSION['lcs-frequency'] == "weekly") echo " selected"; ?>>Weekly</option>
											<option value="biweekly"<? if($_SESSION['lcs-frequency'] == "biweekly") echo " selected"; ?>>Bi-Weekly</option>
										</select></p>
										<p>Lot Size: 
										<select name="lcs-lotsize">
											<option value="small"<? if($_SESSION['lcs-lotsize'] == "small") echo " selected"; ?>>&lt;6,000 sq. ft.</option>
											<option value="medium"<? if($_SESSION['lcs-lotsize'] == "medium") echo " selected"; ?>>6,001 - 12,000 sq. ft.</option>
											<option value="large"<? if($_SESSION['lcs-lotsize'] == "large") echo " selected"; ?>>12,001 - 16,000 sq. ft.</option>
											<option value="maximum"<? if($_SESSION['lcs-lotsize'] == "maximum") echo " selected"; ?>>16,001 - 20,000 sq. ft.</option>
										</select></p>
										<p><a href="http://timberlinelawnandlandscape.com/Lawn_Care.html" target="_blank">view price chart</a></p>
									</div>
									
									<div id="service-details-lcb">
									<h5>Large Lawn Service Details</h5>
										<p><input type="checkbox" name="has-estimate" />I have an estimate</p>
										<p>Service Frequency: 
										<select name="lcb-frequency">
											<option value="weekly"<? if($_SESSION['lcb-frequency'] == "weekly") echo " selected"; ?>>Weekly</option>
											<option value="biweekly"<? if($_SESSION['lcb-frequency'] == "biweekly") echo " selected"; ?>>Bi-Weekly</option>
										</select></p>
										<p><label for="lcb-lotsize">Lot Size:</label><input type="text" name="lcb-lotsize" />sq. ft.</p>
										<p><label for="lcb-estimate">Estimate:</label><input type="text" name="lcb-estimate" /></p>
										<p><label for="lcb-price">Price:</label><input type="text" name="lcb-price" /></p>
									</div>
									
									<div id="service-details-landscape">
									<h5>Landscaping Service Details</h5>
										<p><label for="ls-estimate">Estimate:</label><input type="text" name="ls-estimate" /></p>
										<p><label for="ls-price">Price:</label><input type="text" name="ls-price" /></p>
										<p>This is a authorize only transaction. Your payment will be processed when the work is complete and to your satisfaction.</p>
									</div>
								</div>
							</td>
						</tr>
						<tr>
			            	<td><label for="notes">Additional Information:</label></td>
			                <td><textarea name="notes" rows="2" cols="23"><? echo $_SESSION['address']; ?></textarea></td>
			            </tr>
						
						
							
		            <tr><td colspan="2"><h4>Credit Card Information</h4></td></tr>
		            	<tr>
							<td><label for="creditCardName">Name on card:</label><span class="instruct">As it appears on the credit card</span></td>
							<td><input type="text" size="30" maxlength="60" name="creditCardName" value="<? echo $_SESSION['creditCardName']; ?>" /></td>
						</tr>
						<tr>
							<td><label for="billingaddrsame">Billing address of card:</label></td>
							<td><input type="checkbox" name="billingaddrsame" onclick="set_billing(this)" /> <span class="instruct" style="text-align: left;">(check this box if billing address is the same as service address above)</span></td>
						</tr>
						<tr>
			            	<td><label for="cc_address">Address:</label></td>
			                <td><textarea name="cc_address" rows="2" cols="23"><? echo $_SESSION['cc_address']; ?></textarea></td>
			            </tr> 
			            <tr>
			            	<td><label for="cc_city">City:</label></td>
			                <td><input type="text" size="30" maxlength="80" name="cc_city" value="<? echo $_SESSION['cc_city']; ?>" /></td>
			            </tr>
			            <tr>
			            	<td><label for="cc_state">State:</label></td>
			                <td>
			                	<select name="cc_state">
			                    	<option></option>
			                        <option value="AK"<? if($_SESSION['cc_state'] == "AK") echo " selected"; ?>>AK</option><option value="AL"<? if($_SESSION['cc_state'] == "AL") echo " selected"; ?>>AL</option><option value="AR"<? if($_SESSION['cc_state'] == "AR") echo " selected"; ?>>AR</option><option value="AZ"<? if($_SESSION['cc_state'] == "AZ") echo " selected"; ?>>AZ</option><option value="CA"<? if($_SESSION['cc_state'] == "CA") echo " selected"; ?>>CA</option><option value="CO"<? if($_SESSION['cc_state'] == "CO") echo " selected"; ?>>CO</option><option value="CT"<? if($_SESSION['cc_state'] == "CT") echo " selected"; ?>>CT</option><option value="DC"<? if($_SESSION['cc_state'] == "DC") echo " selected"; ?>>DC</option>
			                        <option value="DE"<? if($_SESSION['cc_state'] == "DE") echo " selected"; ?>>DE</option><option value="FL"<? if($_SESSION['cc_state'] == "FL") echo " selected"; ?>>FL</option><option value="GA"<? if($_SESSION['cc_state'] == "GA") echo " selected"; ?>>GA</option><option value="HI"<? if($_SESSION['cc_state'] == "HI") echo " selected"; ?>>HI</option><option value="IA"<? if($_SESSION['cc_state'] == "IA") echo " selected"; ?>>IA</option><option value="ID"<? if($_SESSION['cc_state'] == "ID") echo " selected"; ?>>ID</option><option value="IL"<? if($_SESSION['cc_state'] == "IL") echo " selected"; ?>>IL</option><option value="IN"<? if($_SESSION['cc_state'] == "IN") echo " selected"; ?>>IN</option>
			                        <option value="KS"<? if($_SESSION['cc_state'] == "KS") echo " selected"; ?>>KS</option><option value="KY"<? if($_SESSION['cc_state'] == "KY") echo " selected"; ?>>KY</option><option value="LA"<? if($_SESSION['cc_state'] == "LA") echo " selected"; ?>>LA</option><option value="MA"<? if($_SESSION['cc_state'] == "MA") echo " selected"; ?>>MA</option><option value="MD"<? if($_SESSION['cc_state'] == "MD") echo " selected"; ?>>MD</option><option value="ME"<? if($_SESSION['cc_state'] == "ME") echo " selected"; ?>>ME</option><option value="MI"<? if($_SESSION['cc_state'] == "MI") echo " selected"; ?>>MI</option><option value="MN"<? if($_SESSION['cc_state'] == "MN") echo " selected"; ?>>MN</option>
									<option value="MO"<? if($_SESSION['cc_state'] == "MO") echo " selected"; ?>>MO</option><option value="MS"<? if($_SESSION['cc_state'] == "MS") echo " selected"; ?>>MS</option><option value="MT"<? if($_SESSION['cc_state'] == "MT") echo " selected"; ?>>MT</option><option value="NC"<? if($_SESSION['cc_state'] == "NC") echo " selected"; ?>>NC</option><option value="ND"<? if($_SESSION['cc_state'] == "ND") echo " selected"; ?>>ND</option><option value="NE"<? if($_SESSION['cc_state'] == "NE") echo " selected"; ?>>NE</option><option value="NH"<? if($_SESSION['cc_state'] == "NH") echo " selected"; ?>>NH</option><option value="NJ"<? if($_SESSION['cc_state'] == "NJ") echo " selected"; ?>>NJ</option>
									<option value="NM"<? if($_SESSION['cc_state'] == "NM") echo " selected"; ?>>NM</option><option value="NV"<? if($_SESSION['cc_state'] == "NV") echo " selected"; ?>>NV</option><option value="NY"<? if($_SESSION['cc_state'] == "NY") echo " selected"; ?>>NY</option><option value="OH"<? if($_SESSION['cc_state'] == "OH") echo " selected"; ?>>OH</option><option value="OK"<? if($_SESSION['cc_state'] == "OK") echo " selected"; ?>>OK</option><option value="OR"<? if($_SESSION['cc_state'] == "OR") echo " selected"; ?>>OR</option><option value="PA"<? if($_SESSION['cc_state'] == "PA") echo " selected"; ?>>PA</option><option value="RI"<? if($_SESSION['cc_state'] == "RI") echo " selected"; ?>>RI</option>
			                        <option value="SC"<? if($_SESSION['cc_state'] == "SC") echo " selected"; ?>>SC</option><option value="SD"<? if($_SESSION['cc_state'] == "SD") echo " selected"; ?>>SD</option><option value="TN"<? if($_SESSION['cc_state'] == "TN") echo " selected"; ?>>TN</option><option value="TX"<? if($_SESSION['cc_state'] == "TX") echo " selected"; ?>>TX</option><option value="UT"<? if($_SESSION['cc_state'] == "UT") echo " selected"; ?>>UT</option><option value="VA"<? if($_SESSION['cc_state'] == "VA") echo " selected"; ?>>VA</option><option value="VT"<? if($_SESSION['cc_state'] == "VT") echo " selected"; ?>>VT</option><option value="WA"<? if($_SESSION['cc_state'] == "WA") echo " selected"; ?>>WA</option>
			                        <option value="WI"<? if($_SESSION['cc_state'] == "WI") echo " selected"; ?>>WI</option><option value="WV"<? if($_SESSION['cc_state'] == "WV") echo " selected"; ?>>WV</option><option value="WY"<? if($_SESSION['cc_state'] == "WY") echo " selected"; ?>>WY</option><option value="AA"<? if($_SESSION['cc_state'] == "AA") echo " selected"; ?>>AA</option><option value="AE"<? if($_SESSION['cc_state'] == "AE") echo " selected"; ?>>AE</option><option value="AP"<? if($_SESSION['cc_state'] == "AP") echo " selected"; ?>>AP</option><option value="AS"<? if($_SESSION['cc_state'] == "AS") echo " selected"; ?>>AS</option><option value="FM"<? if($_SESSION['cc_state'] == "FM") echo " selected"; ?>>FM</option>
									<option value="GU"<? if($_SESSION['cc_state'] == "GU") echo " selected"; ?>>GU</option><option value="MH"<? if($_SESSION['cc_state'] == "MH") echo " selected"; ?>>MH</option><option value="MP"<? if($_SESSION['cc_state'] == "MP") echo " selected"; ?>>MP</option><option value="PR"<? if($_SESSION['cc_state'] == "PR") echo " selected"; ?>>PR</option><option value="PW"<? if($_SESSION['cc_state'] == "PW") echo " selected"; ?>>PW</option><option value="VI"<? if($_SESSION['cc_state'] == "VI") echo " selected"; ?>>VI</option>
								</select>
			                </td>
			            </tr>
			            <tr>
			            	<td><label for="cc_zip">Zip Code:</label></td>
			                <td><input type="text" size="10" maxlength="10" name="cc_zip" value="<? echo $_SESSION['cc_zip']; ?>" /></td>
			            </tr>
			            <tr>
			            	<td><label for="creditCardType">Credit Card Type:</label></td>
			                <td>
			                <select name="creditCardType">
			                    <option value="Visa"<? if($_SESSION['creditCardType'] == "Visa") echo " selected"; ?>>Visa</option>
			    	            <option value="MasterCard"<? if($_SESSION['creditCardType'] == "MasterCard") echo " selected"; ?>>MasterCard</option>
			                    <option value="Discover"<? if($_SESSION['creditCardType'] == "Discover") echo " selected"; ?>>Discover</option>
				                <option value="Amex"<? if($_SESSION['creditCardType'] == "Amex") echo " selected"; ?>>American Express</option>
			                </select>
			                </td>
			            </tr>
			            <tr>
			            	<td><label for="creditCardNumber">Credit Card Number:</label></td>
			                <td><input type="text" size="30" maxlength="19" name="creditCardNumber" value="<? echo $_SESSION['creditCardNumber']; ?>" /></td>
			            </tr>
			            <tr>
			            	<td><label for="expDateMonth">Card Expiration Date:</label><span class="instruct">Month / Year</span></td>
			                <td>
			                <select name="expDateMonth">
			                    <option value="">MM</option>
			                    <?php
			                    for ($m = 1; $m <= 12; $m++) {
			                        if($m < 10) $m = "0$m";
			                        echo "<option value=\"$m\""; if($_SESSION['expDateMonth'] == $m) echo " selected"; echo ">$m</option>\n";
			                    }
			                    ?>
			                </select> /
			                <select name="expDateYear">
			                    <option value="">YYYY</option>
			                    <?php
			                    $totayYear = date('Y');
			                    for ($i = $totayYear; $i < $totayYear+10; $i++) {
			                        echo "<option value=\"$i\""; if($_SESSION['expDateYear'] == $i) echo " selected"; echo ">$i</option>\n";
			                    }
			                    ?>
			                </select>
			                </td>
			            </tr>
			            <tr>
			            	<td>
			                	<label for="cvv2Number">Credit Card Code (<abbr title="Card Verification Value">CVV</abbr>):</label>
			                    <span class="instruct">For Visa, Mastercard, and Discover cards, the card code is the last 3 digit located on the back of your card on or above your signature line.</span>
			                </td>
			                <td><input type="text" size="4" maxlength="4" name="cvv2Number" value="<? echo $_SESSION['cvv2Number']; ?>" /></td>
			            </tr>
			            
			            
			            
			            <tr>
							<td><label for="referral">How did you hear about us?</label></td>
							<td>
							<select name="referral">
								<option value="select" selected="selected">Please Select</option>
								<option value="searchengine"<? if($_SESSION['referral'] == "searchengine") echo " selected=\"selected\""; ?>>Internet Search Engine</option>
								<option value="doorhanger"<? if($_SESSION['referral'] == "doorhanger") echo " selected=\"selected\""; ?>>Door Hanger</option>
								<option value="directmail"<? if($_SESSION['referral'] == "directmail") echo " selected=\"selected\""; ?>>Direct Mail</option>
								<option value="phonebook"<? if($_SESSION['referral'] == "phonebook") echo " selected=\"selected\""; ?>>Phone Book</option>
								<option value="customerreferral"<? if($_SESSION['referral'] == "customerreferral") echo " selected=\"selected\""; ?>>Customer Referral</option>
								<option value="other"<? if($_SESSION['referral'] == "other") echo " selected=\"selected\""; ?>>Other</option>
							</select>
							</td>
						</tr>
			            
			            
					<tr>
						<td colspan="2" class="center">
							<p><input type="checkbox" name="agreement" /> I agree to the <a href="http://timberlinelawnandlandscape.com/Terms_of_Service.html" target="_blank">terms of service</a>.</p>
							<input name="button" type="submit" id="button" value="Submit Order" />
						</td>
					</tr>

		        </table>
        	</form> 
		</div>
    </div>
    
    <div id="footer">
    	<p>Richard Lavery | Owner | Timberline Lawn &amp; Landscape | (214) 557-6975 | <a href="mailto:timberlinelawnandlandscape@gmail.com">timberlinelawnandlandscape@gmail.com</a></p>
    	<p><a href="http://networkstrategics.com/" style="text-decoration: none; color: #000;">Payment gateway integration</a> by <a href="http://networkstrategics.com/">Network Strategics</a></p>
    </div>
</div>
</body>
</html>