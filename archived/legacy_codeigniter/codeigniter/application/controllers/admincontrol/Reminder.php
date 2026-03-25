<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Reminder extends Admin_Controller
{

	public function __construct()
	{
		parent::__construct();
//        date_default_timezone_set("Asia/Karachi");
	}

	public function index()
	{

//		if(!$this->input->is_cli_request())
//		{
//			echo "This script can only be accessed via the command line" . PHP_EOL;
//			return;
//		}

		$suppliers = $this->admin_m->get_All_SupplierCatalog_Set();
		$date_object = date("Y-m-d");
		if ($this->admin_m->get_Notification_SettingByKey('is_price_expiry')) {

			foreach ($suppliers as $supplierItem) {

				$date = new DateTime($supplierItem->supcat_lastdate); // For today/now, don't pass an arg.
				$date1 = new DateTime($supplierItem->supcat_lastdate); // For today/now, don't pass an arg.
				$date->modify("-" . $this->admin_m->get_Notification_SettingByKey('price_expiry_no_of_days') . " day");
				if ($date_object >= $date->format("Y-m-d") && $date_object <= $date1->format("Y-m-d")) {
					$template = $this->admin_m->GetEmailTemplateWithID($this->admin_m->get_Notification_SettingByKey('price_expiry_template'));
					$setting = $this->admin_m->get_CompanySMTP_Setting();

					$toEmail = [
						[
							'email' => $supplierItem->sup_email,
							'name' => $supplierItem->sup_name,
						],
					];

					if ($template->email_cc != null) {
						$cc = [
							$template->email_cc
						];
					} else {
						$cc = [];
					}

					$summaryTable = '<table class="MsoNormalTable" border="1" cellpadding="0" width="550" style="width:412.5pt;
mso-cellspacing:1.5pt;margin-left:7.5pt;border-top:solid #DDDDDD 1.0pt;
border-left:none;border-bottom:solid #DDDDDD 1.0pt;border-right:none;
mso-border-top-alt:solid #DDDDDD .75pt;mso-border-bottom-alt:solid #DDDDDD .75pt;
mso-yfti-tbllook:1184;box-sizing:inherit">
									 <tbody>';

					$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Item Name<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Item Code<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Price<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Daily Price<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Weekly<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Monthly<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">Expiry Date<o:p></o:p></span></b></p>
									  </td>
									 </tr>';

					$summaryTable .= '<tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;height:22.5pt;box-sizing:inherit">
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->item_name . '<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->item_code . '<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->supcat_price . '<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;background:#DDDDDD;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->supcat_daily_price . '<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->supcat_weekly_price . '<o:p></o:p></span></b></p>
									  </td>
									  <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->supcat_monthly_price . '<o:p></o:p></span></b></p>
									  </td>
									   <td width="30%" nowrap="" valign="top" style="width:30.0%;border:none;border-right:
									  solid #E0E0E0 1.0pt;mso-border-right-alt:solid #E0E0E0 .75pt;
									  padding:3.0pt 3.0pt 3.0pt 3.0pt;height:22.5pt">
									  <p class="MsoNormal" align="center" style="margin-bottom:7.5pt;text-align:center"><b><span style="font-size:10.0pt;font-family:&quot;Lato&quot;,sans-serif;mso-fareast-font-family:
									  &quot;Times New Roman&quot;;color:#444749">' . $supplierItem->supcat_lastdate . '<o:p></o:p></span></b></p>
									  </td>
								
									 </tr>';

					$summaryTable .= '</tbody>
									</table>';

					$params = [
						"#PriceExpiryTable#" => $summaryTable,
					];

					$data = $this->admin_m->prepareEmailBody($template->email_key, $params);

					return $this->sendSMTPEmail($toEmail, $data['subject'], $data['content'], $cc, $setting);
				}
			}

		}
	}
}
