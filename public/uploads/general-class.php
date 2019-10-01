<?php
function getNumbers($name,$maxLimit,$event="",$selected=""){

	$html="<select name='".$name."' ".$event.">";
		for($i=0;$i<=$maxLimit;$i++){
		if($selected==$i) $select="selected";
		else $select ="";
		 $html.="<option value=$i ".$select.">$i</option>";
		}
	  $html.="</select>";
	 return $html;
}

function getwash($name){
	$html="<select name='".$name."' style='width:80px'  onChange=\"displayOther(this,'OthDiv".$name."','Oth".$name."'); return false;\">
				<option value='None'>None</option>
				<option value='Botanical Wash'>Botanical Wash</option>
				<option value='Kaleidoscope Wash'>Kaleidoscope Wash</option>
				<option value='Lava Wash'>Lava wash</option>
				<option value='Crystal Wash'>Crystal Wash</option>	
				<option value='Crystal Wash'>Crystal Wash</option>
				<option value='Ball tie Dye'>Ball tie Dye</option>
				<option value='Distress Wash'>Distress Wash</option>
				<option value='Antique Wash'>Antique Wash</option>
				<option value='Alligator Wash'>Alligator Wash</option>
				<option value='Random Tie Dye'>Random Tie Dye</option>
				<option value='Circle Tie'>Circle Tie</option>
				<option value='Waterfall Wash'>Waterfall Wash</option>
				<option value='Polo Wash'>Polo Wash</option>	
				<option value='Plaid Wash'>Plaid Wash</option>
				<option value='Painters Wash'>Painters Wash</option>
				<option value='Paper Wash'>Paper Print</option>
				<option value='Dye print'>Dye print</option>
				<option value='Dip And Drip'>Dip And Drip</option>
				<option value='Tie Dye'>Tie Dye</option>
				<option value='Spray'>Spray</option>
				<option value='Dip Dye'>Dip Dye</option>
				<option value='Ombre'>Ombre</option>	
				<option value='Cloud Wash'>Cloud Wash</option>
				<option value='Sunburst Tie Dye'>Sunburst Tie Dye</option>
				<option value='Swirl Tie Dye'>Swirl Tie Dye</option>
				<option value='Tapestry'>Tapestry</option>
				<option value='G.Dye'>G.Dye</option>
				<option value='Other'>Other</option>
	</select>";

	 return $html;
}

function getGarmentsType($name){
$html="<select name='".$name."' style='width:80px'   onChange=\"displayOther(this,'OthDiv".$name."','Oth".$name."'); return false;\">
			<option value='None'>None</option>
			<option value='Tank'>Tank</option>
			<option value='S/S Tee Shirt'>S/S Tee Shirt</option>
			<option value='Long sleeve shirt'>Long sleeve shirt</option>
			<option value='Henley -short sleeve'>Henley -short sleeve</option>
			<option value='Henley -long sleeve'>Henley -long sleeve</option>
			<option value='Tunic- long sleeve'>Tunic- long sleeve</option>
			<option value='Tunic- short sleeve'>Tunic- short sleeve</option>  
			<option value='Skirt-short'>Skirt-short</option>
			<option value='Skirt-medium'>Skirt-medium</option>
			<option value='Skirt-Long'>Skirt-Long </option>
			<option value='Scarf'>Scarf</option>
			<option value='Fabric block - small'>Fabric block - small</option>
			<option value='Fabric block -medium'>Fabric block -medium</option> 
			<option value='Fabric block -large'>Fabric block -large</option>
			<option value='Panel - small'>Panel - small</option>
			<option value='Panel - medium'>Panel - medium</option>
			<option value='Panel- large'>Panel- large</option>
			<option value='Dress- short (Above knee)'>Dress- short (Above knee)</option>
			<option value='Dress-regular (Knee Length)'>Dress-regular (Knee Length)</option> 
			<option value='Dress-long (ankle length)'>Dress-long (ankle length)</option>
			<option value='Hoodie- long sleeve'>Hoodie- long sleeve</option>
			<option value='Hoodie short sleeve'>Hoodie short sleeve</option>
			<option value='Skirt-short'>Skirt-short</option>
			<option value='Skirt-regular'>Skirt-regular</option>
			<option value='Skirt-long'>Skirt-long</option>
			<option value='Pants'>Pants</option>
			<option value='Jeans'>Jeans</option>
			<option value='Leggings'>Leggings</option> 
			<option value='tights'>tights</option>
			<option value='Jacket long slv'>Jacket long slv</option>
			<option value='Jacket short slv'>Jacket short slv</option>
			<option value='Jacket with hood L.Slv'>Jacket with hood L.Slv</option>
			<option value='Jacket with hood S.Slv'>Jacket with hood S.Slv</option>
			<option value='Other'>Other</option>

</select>";
return $html;	
}


 function sendMail(){
		 $name=$_SESSION[customer]." ".date("Y-m-d")." "."novelty ";
$fname = $name.".doc";
		$fileatt = $fname; // Path to the file
		$fileatt_type = "application/octet-stream"; // File Type
		$fileatt_name = $fname; // Filename that will be used for the file as the attachment
		
		$email_from = "rafi<rafi@caravandyehouse.com>";  // Who the email is from
		$email_subject = $name; // The Subject of the email
		$email_txt = "Please find the attachement"; // Message that the email has in it
		
		$email_to = "barri@caravandyehouse.com,rafi@caravandyehouse.com"; // Who the email is too
		
		$headers = "From: ".$email_from;
		
		$file = fopen($fileatt,'rb');
		$data = fread($file,filesize($fileatt));
		fclose($file);
		
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		
		$headers .= "\nMIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";
		
		$email_message .= "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" .
		$email_message . "\n\n";
		
		$data = chunk_split(base64_encode($data));
		
		$email_message .= "--{$mime_boundary}\n" .
		"Content-Type: {$fileatt_type};\n" .
		" name=\"{$fileatt_name}\"\n" .
		//"Content-Disposition: attachment;\n" .
		//" filename=\"{$fileatt_name}\"\n" .
		"Content-Transfer-Encoding: base64\n\n" .
		$data . "\n\n" .
		"--{$mime_boundary}--\n";
		
		$ok = @mail($email_to, $email_subject, $email_message, $headers);
		
		if($ok) {
		  session_destroy(); 
		$res="success";
		
		} else {
		$res="fail";
		} 
		return $res;
}
?>
