<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title> </title>
</head>
<style>
	#fr-table{
		border-collapse: collapse;
		font-size: 14px;
	}
	#fr-table th{
		background-color: #0e3672;
    	color: #fff;
	}
	#fr-table td, #fr-table th {
		border: 1px solid #6e6c6c;
		padding: 5px;
	}
</style>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background: #fff;">
	<center>

		<table border="0" cellpadding="0" cellspacing="0" style="padding:0px;margin:0px;width:100%;background: #fff;">
			<tbody>
				<tr>
					<td style="padding:0px;margin:0px;">&nbsp;</td>
					<td style="padding:5px;margin:0px;background: #FFF;" width="650">
						<table border="0" cellpadding="0" cellspacing="0" style="padding:0px;margin:0px;width:100%;border: 1px solid #cdcdcd;">
							<tbody>
								<tr>
									<td align="center" valign="top" style="padding:20px 15px 20px 15px;border-bottom: 1px solid #cdcdcd;">
										<div>
											<img
												src="{{ asset('images/logo.png') }}"
												style="height: 60px;">
										</div>

									</td>
								</tr>
								<tr>
									<td align="center" valign="top" style="padding:20px 15px 0px 15px;">
										<!-- BEGIN BODY // -->
										<table style="" border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td valign="top" style="padding: 10px 18px 10px;">

													<div
														style="text-align:center;color: #222;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size: 18px;font-style: normal;font-weight: bold;line-height: 150%;padding: 0;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
														<!-- COMPRA EN LÍNEA -->
														{{$title}}
													</div>
												</td>
											</tr>
											<tr>
												<td valign="top" style="padding: 10px 18px 10px;">

														  
													<div style="color: #444;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size: 15px;font-style: normal;font-weight: normal;line-height: 150%;text-align: left;padding: 0;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
														<!-- Estimado cliente,<br>
														¡Gracias por realizar su compra! -->
														<?php
															echo $mensaje;
														?>
													</div>

												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td valign="top"
										style="padding: 30px 18px 10px;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break:break-word; color: #777;text-align: center">
										OMEGA &copy;
									</td>
								</tr>
						</table>
					</td>
					<td style="padding:0px;margin:0px;">&nbsp;</td>
				</tr>
			</tbody>
		</table>

	</center>
</body>

</html>