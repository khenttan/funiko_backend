<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Session::get('titan.settings.name'); ?></title>
	<style>
	    body{margin:0; font-family:arial; color:#333; font-size:14px; line-height:20px;}
	    table tr td{vertical-align:top;}
	</style>
  </head>
  <body>

    	<table width="1000px" align="center" cellpadding="0" cellspacing="0" style="background-color:#fff;">
			<tbody>
				<tr>
					<td style=" padding:10px 15px;">
					    <a href="<?php echo url('/'); ?>" style="color:#FFFFFF; text-decoration: none;">
							<img src="{{asset('assets/images/logo.png')}}" alt="img" width="75px">
                        </a>
					</td>
				</tr>
				<tr>
					<td>
						<table width="1000px" height="400px" align="center" cellpadding="0" cellspacing="0"  style="padding:15px;">
							<tr>
								<td><?php echo config('app.APP_URL'); ?><?php echo $messageBody ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" style="background-color:#f09922; text-align:center; padding:15px 10px 10px 10px;">
						<table style="margin:0 auto;">
							<tr>
                                <td style="color:#fff;">
              						&copy; Copyright All Rights Reserved, <a href="#" style="color:#fff;text-decoration: none;" >Recycle</a> <?php echo date('Y'). '.'; ?> 
								</td>
							</tr>
                        </table>
					</td>
				</tr>
			</tbody>
		</table>
  </body>
</html>
