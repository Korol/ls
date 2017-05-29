<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'news';

$route['login'] = 'authentication/login';
$route['logout'] = 'authentication/logout';

// Маршрутизация для раздела "Новости"
$route['news/(:num)/edit']      = 'News/edit/$1';
$route['news/(:num)/remove']    = 'News/remove/$1';

// Маршрутизация для раздела "Документация"
$route['documents/(:num)/upload'] = 'Documents/upload/$1';
$route['documents/(:num)/server'] = 'Documents/server/$1';

// Маршрутизация для раздела "Сотрудники"
$route['employee/(:num)/profile']   = 'Employee/profile/$1';
$route['employee/(:num)/rights']    = 'Employee/rights/$1';
$route['employee/(:num)/update']    = 'Employee/update/$1';
$route['employee/(:num)/avatar']    = 'Employee/avatar/$1';
$route['employee/(:num)/remove']    = 'Employee/remove/$1';
$route['employee/(:num)/restore']   = 'Employee/restore/$1';

$route['employee/(:num)/agreement/data']            = 'Employee_Agreement/data/$1';
$route['employee/(:num)/agreement/upload']          = 'Employee_Agreement/upload/$1';
$route['employee/(:num)/agreement/server']          = 'Employee_Agreement/server/$1';
$route['employee/(:num)/agreement/(:num)/view']     = 'Employee_Agreement/show/$1/$2';
$route['employee/(:num)/agreement/(:num)/load']     = 'Employee_Agreement/load/$1/$2';
$route['employee/(:num)/agreement/(:num)/remove']   = 'Employee_Agreement/remove/$1/$2';

$route['employee/(:num)/passport/data']             = 'Employee_Passport/data/$1';
$route['employee/(:num)/passport/upload']           = 'Employee_Passport/upload/$1';
$route['employee/(:num)/passport/server']           = 'Employee_Passport/server/$1';
$route['employee/(:num)/passport/(:num)/view']      = 'Employee_Passport/show/$1/$2';
$route['employee/(:num)/passport/(:num)/load']      = 'Employee_Passport/load/$1/$2';
$route['employee/(:num)/passport/(:num)/remove']    = 'Employee_Passport/remove/$1/$2';

$route['employee/(:num)/children/data']             = 'Employee_Children/data/$1';
$route['employee/(:num)/children/save']             = 'Employee_Children/save/$1';
$route['employee/(:num)/children/(:num)/remove']    = 'Employee_Children/remove/$1/$2';

$route['employee/(:num)/site/data']                 = 'Employee_Site/data/$1';
$route['employee/(:num)/site/save']                 = 'Employee_Site/save/$1';
$route['employee/(:num)/site/(:num)/remove']        = 'Employee_Site/remove/$1/$2';

$route['employee/(:num)/site/(:num)/customer/data']             = 'Employee_Site_Clients/data/$1/$2';
$route['employee/(:num)/site/(:num)/customer/(:num)/find']      = 'Employee_Site_Clients/find/$1/$2/$3';
$route['employee/(:num)/site/(:num)/customer/(:num)/save']      = 'Employee_Site_Clients/save/$1/$2/$3';
$route['employee/(:num)/site/(:num)/customer/(:num)/remove']    = 'Employee_Site_Clients/remove/$1/$2/$3';

$route['employee/(:num)/relative/data']             = 'Employee_Relative/data/$1';
$route['employee/(:num)/relative/save']             = 'Employee_Relative/save/$1';
$route['employee/(:num)/relative/(:num)/remove']    = 'Employee_Relative/remove/$1/$2';

$route['employee/(:num)/email/data']                = 'Employee_Email/data/$1';
$route['employee/(:num)/email/save']                = 'Employee_Email/save/$1';
$route['employee/(:num)/email/(:num)/remove']       = 'Employee_Email/remove/$1/$2';

$route['employee/(:num)/phone/data']                = 'Employee_Phone/data/$1';
$route['employee/(:num)/phone/save']                = 'Employee_Phone/save/$1';
$route['employee/(:num)/phone/(:num)/remove']       = 'Employee_Phone/remove/$1/$2';

$route['employee/(:num)/skype/data']                = 'Employee_Skype/data/$1';
$route['employee/(:num)/skype/save']                = 'Employee_Skype/save/$1';
$route['employee/(:num)/skype/(:num)/remove']       = 'Employee_Skype/remove/$1/$2';

$route['employee/(:num)/socnet/data']               = 'Employee_Socnet/data/$1';
$route['employee/(:num)/socnet/save']               = 'Employee_Socnet/save/$1';
$route['employee/(:num)/socnet/(:num)/remove']      = 'Employee_Socnet/remove/$1/$2';

// Маршрутизация для раздела "Клиенты"
$route['customer/(:num)/profile']   = 'Customer/profile/$1';
$route['customer/(:num)/update']    = 'Customer/update/$1';
$route['customer/(:num)/avatar']    = 'Customer/avatar/$1';
$route['customer/(:num)/remove']    = 'Customer/remove/$1';
$route['customer/(:num)/rights']    = 'Customer/rights/$1';

$route['customer/(:num)/passport/data']             = 'Customer_Passport/data/$1';
$route['customer/(:num)/passport/(:num)/load']      = 'Customer_Passport/load/$1/$2';
$route['customer/(:num)/passport/upload']           = 'Customer_Passport/upload/$1';
$route['customer/(:num)/passport/server']           = 'Customer_Passport/server/$1';
$route['customer/(:num)/passport/(:num)/remove']    = 'Customer_Passport/remove/$1/$2';

$route['customer/(:num)/question/photo/data']             = 'Customer_QuestionPhoto/data/$1';
$route['customer/(:num)/question/photo/(:num)/load']      = 'Customer_QuestionPhoto/load/$1/$2';
$route['customer/(:num)/question/photo/upload']           = 'Customer_QuestionPhoto/upload/$1';
$route['customer/(:num)/question/photo/server']           = 'Customer_QuestionPhoto/server/$1';
$route['customer/(:num)/question/photo/(:num)/remove']    = 'Customer_QuestionPhoto/remove/$1/$2';

$route['customer/(:num)/album/data']                = 'Customer_Album/data/$1';
$route['customer/(:num)/album/add']                 = 'Customer_Album/add/$1';
$route['customer/(:num)/album/(:num)/remove']       = 'Customer_Album/remove/$1/$2';
$route['customer/(:num)/album/cross/(:num)/remove'] = 'Customer_Album/remove_cross/$1/$2';
$route['customer/(:num)/album/(:num)/upload']       = 'Customer_Album/upload/$1/$2';
$route['customer/(:num)/album/(:num)/server']       = 'Customer_Album/server/$1/$2';

$route['customer/(:num)/agreement/data']            = 'Customer_Agreement/data/$1';
$route['customer/(:num)/agreement/upload']          = 'Customer_Agreement/upload/$1';
$route['customer/(:num)/agreement/server']          = 'Customer_Agreement/server/$1';
$route['customer/(:num)/agreement/(:num)/view']     = 'Customer_Agreement/show/$1/$2';
$route['customer/(:num)/agreement/(:num)/load']     = 'Customer_Agreement/load/$1/$2';
$route['customer/(:num)/agreement/(:num)/remove']   = 'Customer_Agreement/remove/$1/$2';

$route['customer/(:num)/language/data']             = 'Customer_Language/data/$1';
$route['customer/(:num)/language/save']             = 'Customer_Language/save/$1';
$route['customer/(:num)/language/(:num)/remove']    = 'Customer_Language/remove/$1/$2';

$route['customer/(:num)/children/data']             = 'Customer_Children/data/$1';
$route['customer/(:num)/children/save']             = 'Customer_Children/save/$1';
$route['customer/(:num)/children/(:num)/remove']    = 'Customer_Children/remove/$1/$2';

$route['customer/(:num)/email/data']                = 'Customer_Email/data/$1';
$route['customer/(:num)/email/save']                = 'Customer_Email/save/$1';
$route['customer/(:num)/email/(:num)/remove']       = 'Customer_Email/remove/$1/$2';

$route['customer/(:num)/site/data']                 = 'Customer_Site/data/$1';
$route['customer/(:num)/site/save']                 = 'Customer_Site/save/$1';
$route['customer/(:num)/site/(:num)/remove']        = 'Customer_Site/remove/$1/$2';

$route['customer/(:num)/story/data']                = 'Customer_Story/data/$1';
$route['customer/(:num)/story/save']                = 'Customer_Story/save/$1';
$route['customer/(:num)/story/(:num)/remove']       = 'Customer_Story/remove/$1/$2';

$route['question/template']         = 'Customer_Question/template/$1';
$route['question/template/add']     = 'Customer_Question/template_add/$1';
$route['question/template/edit']    = 'Customer_Question/template_edit/$1';
$route['question/template/remove']  = 'Customer_Question/template_remove/$1';

$route['customer/(:num)/question/data'] = 'Customer_Question/data/$1';
$route['customer/(:num)/question/save'] = 'Customer_Question/save/$1';

$route['customer/(:num)/history/data'] = 'Customer_History/data/$1';

$route['customer/(:num)/video/site/data']           = 'Customer_Video_Site/data/$1';
$route['customer/(:num)/video/site/save']           = 'Customer_Video_Site/save/$1';
$route['customer/(:num)/video/site/(:num)/remove']  = 'Customer_Video_Site/remove/$1/$2';

$route['customer/(:num)/video/site/(:num)/video_0/data']    = 'Customer_Video_Site_Link/data/$1/$2/0';
$route['customer/(:num)/video/site/(:num)/video_1/data']    = 'Customer_Video_Site_Link/data/$1/$2/1';
$route['customer/(:num)/video/site/(:num)/video_2/data']    = 'Customer_Video_Site_Link/data/$1/$2/2';
$route['customer/(:num)/video/site/link/add']               = 'Customer_Video_Site_Link/add/$1';
$route['customer/(:num)/video/site/link/(:num)/remove']     = 'Customer_Video_Site_Link/remove/$1/$2';

$route['customer/(:num)/video_0/data']              = 'Customer_Video/data/$1/0';
$route['customer/(:num)/video_1/data']              = 'Customer_Video/data/$1/1';
$route['customer/(:num)/video/add']                 = 'Customer_Video/add/$1';
$route['customer/(:num)/video/(:num)/remove']       = 'Customer_Video/remove/$1/$2';

// Мужчины
$route['customer/mens/save'] = 'Customer_Mens/save';
$route['customer/mens/remove'] = 'Customer_Mens/remove';

// Маршрутизация для раздела "Отчеты"
$route['reports/daily/(:any)']              = 'Reports_Daily/$1';
$route['reports/mailing/(:any)']            = 'Reports_Mailing/$1';
$route['reports/salary/(:any)']             = 'Reports_Salary/$1';
$route['reports/correspondence/(:any)']     = 'Reports_Correspondence/$1';
$route['reports/general/customers/(:any)']  = 'Reports_GeneralOfCustomers/$1';
$route['reports/overlay/salary/(:any)']     = 'Reports_OverlaySalary/$1';
$route['reports/approved/salary/(:any)']    = 'Reports_ApprovedSalary/$1';
$route['reports/general/salary/(:any)']     = 'Reports_GeneralSalary/$1';
$route['reports/overall/allocation/(:any)'] = 'Reports_OverallAllocation/$1';

// Маршрутизация для раздела "Отчеты" - LoveStory
$route['reports/lovestory/daily/(:any)']                = 'Reports_LoveStory_Daily/$1';
$route['reports/lovestory/daily/plan/(:any)']           = 'Reports_LoveStory_Daily_Plan/$1';
$route['reports/lovestory/daily/plan/agency/(:any)']    = 'Reports_LoveStory_Daily_Plan_Agency/$1';
$route['reports/lovestory/general/(:any)']              = 'Reports_LoveStory_GeneralOfEmployees/$1';
$route['reports/lovestory/allocation/(:any)']           = 'Reports_Allocation/$1';

// Маршрутизация для раздела "Обучение"
$route['training/(:num)']               = 'Training/index/$1';
$route['training/(:num)/add/file']      = 'Training/add/$1';
$route['training/(:num)/edit/(:num)']   = 'Training/edit/$1/$2';
$route['training/(:num)/show/(:num)']   = 'Training/show/$1/$2';

// Маршрутизация для раздела "Задачи"
$route['tasks/(:num)/(:any)']           = 'Tasks/$2/$1';
$route['task/(:num)/comment/(:any)']    = 'TaskComment/$2/$1';

// Маршрутизация для раздела "Услуги"
$route['services/western/(:any)']        = 'Services_Western/$1';
$route['services/western/(:num)/edit']   = 'Services_Western/edit/$1';
$route['services/meeting/(:any)']        = 'Services_Meeting/$1';
$route['services/meeting/(:num)/edit']   = 'Services_Meeting/edit/$1';
$route['services/delivery/(:any)']       = 'Services_Delivery/$1';
$route['services/delivery/(:num)/edit']  = 'Services_Delivery/edit/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
